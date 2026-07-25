<?php

namespace App\Services\Shopping;

use App\Models\BillingAddress;
use App\Models\CartItem;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;
use App\Models\ShippingRate;
use App\Models\ShoppingCart;
use App\Models\TaxRule;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CheckoutService
{
    public function __construct(private readonly InternationalCommerceService $international) {}

    public function checkout(User $user, array $attributes): Order
    {
        return DB::transaction(function () use ($user, $attributes): Order {
            $cart = ShoppingCart::query()->lockForUpdate()->where('user_id', $user->id)->first();
            if (! $cart) {
                throw ValidationException::withMessages(['cart' => ['Your cart is empty.']]);
            }
            $items = CartItem::query()->with(['product', 'variant'])->lockForUpdate()->where('shopping_cart_id', $cart->id)->get();
            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => ['Your cart is empty.']]);
            }

            $shipping = ShippingAddress::query()->create(['user_id' => $user->id, ...$attributes['shipping_address']]);
            $billing = BillingAddress::query()->create(['user_id' => $user->id, ...($attributes['billing_address'] ?? $attributes['shipping_address'])]);
            $lines = $items->map(fn (CartItem $item) => $this->lockedLine($item));
            $subtotal = round($lines->sum('line_total'), 2);
            [$coupon, $discount] = $this->discount($attributes['coupon_code'] ?? null, $subtotal);
            [$methodId, $shippingTotal] = $this->shipping($attributes['shipping_rate_id'] ?? null, $shipping->country, $subtotal);
            $taxTotal = $this->tax($shipping, $subtotal - $discount);
            $international = null;
            if (Country::query()->where('code', strtoupper($shipping->country))->where('is_active', true)->exists()) {
                $international = $this->international->estimate([
                    'country' => $shipping->country, 'currency' => $attributes['currency'] ?? $cart->currency, 'locale' => $attributes['locale'] ?? null,
                ], $subtotal - $discount, $lines->pluck('product'));
                $shippingTotal = ($attributes['shipping_rate_id'] ?? null) ? $shippingTotal : $international['shipping_total'];
                $taxTotal = $international['tax_total'];
            }
            $dutyTotal = $international['duty_total'] ?? 0.0;
            $total = round($subtotal - $discount + $shippingTotal + $taxTotal + $dutyTotal, 2);

            $order = Order::query()->create([
                'number' => 'VEL-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
                'user_id' => $user->id, 'shipping_address_id' => $shipping->id, 'billing_address_id' => $billing->id,
                'shipping_method_id' => $methodId, 'coupon_id' => $coupon?->id, 'status' => 'pending',
                'payment_status' => 'pending', 'currency' => $international['currency'] ?? $cart->currency, 'locale' => $international['language'] ?? null,
                'exchange_rate' => $international['exchange_rate'] ?? 1, 'warehouse_id' => ($international['warehouse'] ?? null)?->id,
                'shipping_carrier_id' => ($international['carrier'] ?? null)?->id, 'subtotal' => $subtotal,
                'discount_total' => $discount, 'shipping_total' => $shippingTotal, 'tax_total' => $taxTotal, 'duty_total' => $dutyTotal, 'grand_total' => $total,
                'notes' => $attributes['notes'] ?? null,
            ]);
            foreach ($lines as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id, 'product_id' => $line['product_id'], 'product_variant_id' => $line['product_variant_id'],
                    'product_name' => $line['product_name'], 'sku' => $line['sku'], 'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'], 'line_total' => $line['line_total'],
                ]);
                $this->reserve($line['product'], $line['variant'], $line['quantity'], $user, $order);
            }
            if ($coupon) {
                $coupon->increment('usage_count');
                CouponUsage::query()->create(['coupon_id' => $coupon->id, 'order_id' => $order->id, 'user_id' => $user->id, 'discount_amount' => $discount]);
            }
            Payment::query()->create(['order_id' => $order->id, 'provider' => $attributes['payment_provider'], 'status' => 'pending', 'amount' => $total, 'currency' => $cart->currency]);
            if ($international) {
                $this->international->persistEstimate($international, $order->id);
            }
            OrderStatusHistory::query()->create(['order_id' => $order->id, 'user_id' => $user->id, 'to_status' => 'pending', 'note' => 'Order created.']);
            CartItem::query()->where('shopping_cart_id', $cart->id)->delete();
            DB::afterCommit(fn () => $user->notify(new OrderStatusNotification($order, 'order')));

            return $order->load(['items', 'shippingAddress', 'billingAddress', 'shippingMethod', 'history']);
        }, 3);
    }

    private function lockedLine(CartItem $item): array
    {
        $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);
        if ($product->status !== 'published') {
            throw ValidationException::withMessages(['cart' => ["{$product->name} is no longer available."]]);
        }
        $variant = $item->product_variant_id ? ProductVariant::query()->lockForUpdate()->findOrFail($item->product_variant_id) : null;
        if ($variant && ($variant->product_id !== $product->id || $variant->status !== 'active')) {
            throw ValidationException::withMessages(['cart' => ['A selected variant is no longer available.']]);
        }
        $unitPrice = round((float) ($product->sale_price ?? $product->price) + (float) ($variant?->price_difference ?? 0), 2);

        return ['product' => $product, 'variant' => $variant, 'product_id' => $product->id, 'product_variant_id' => $variant?->id, 'product_name' => $product->name, 'sku' => $variant?->sku ?? $product->sku, 'quantity' => $item->quantity, 'unit_price' => $unitPrice, 'line_total' => round($unitPrice * $item->quantity, 2)];
    }

    private function reserve(Product $product, ?ProductVariant $variant, int $quantity, User $user, Order $order): void
    {
        $inventory = Inventory::query()->firstOrCreate(
            ['product_id' => $product->id, 'product_variant_id' => $variant?->id, 'location' => 'primary'],
            ['current_stock' => $variant?->stock_quantity ?? $product->stock_quantity]
        );
        $inventory = Inventory::query()->lockForUpdate()->findOrFail($inventory->id);
        if ($inventory->current_stock < $quantity) {
            throw ValidationException::withMessages(['cart' => ["Insufficient stock for {$product->name}."]]);
        }
        $inventory->decrement('current_stock', $quantity);
        $inventory->increment('reserved_stock', $quantity);
        if ($variant) {
            $variant->decrement('stock_quantity', $quantity);
        } else {
            $product->decrement('stock_quantity', $quantity);
        }
        InventoryLog::query()->create(['inventory_id' => $inventory->id, 'user_id' => $user->id, 'quantity_change' => -$quantity, 'reason' => 'order_reservation', 'reference' => $order->number]);
    }

    private function discount(?string $code, float $subtotal): array
    {
        if (! $code) {
            return [null, 0.0];
        }
        $coupon = Coupon::query()->lockForUpdate()->where('code', $code)->first();
        if (! $coupon || ! $coupon->is_active || ($coupon->starts_at && $coupon->starts_at->isFuture()) || ($coupon->expires_at && $coupon->expires_at->isPast()) || ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) || ($coupon->minimum_order_amount && $subtotal < (float) $coupon->minimum_order_amount)) {
            throw ValidationException::withMessages(['coupon_code' => ['This coupon cannot be applied.']]);
        }
        $discount = $coupon->type === 'percentage' ? $subtotal * ((float) $coupon->value / 100) : (float) $coupon->value;
        if ($coupon->maximum_discount_amount) {
            $discount = min($discount, (float) $coupon->maximum_discount_amount);
        }

        return [$coupon, round(min($discount, $subtotal), 2)];
    }

    private function shipping(?int $rateId, string $country, float $subtotal): array
    {
        if (! $rateId) {
            return [null, 0.0];
        }
        $rate = ShippingRate::query()->with('method')->whereKey($rateId)->where('is_active', true)->first();
        if (! $rate || ! $rate->method->is_active || ($rate->country && strtoupper($rate->country) !== strtoupper($country))) {
            throw ValidationException::withMessages(['shipping_rate_id' => ['This shipping rate is not available for the address.']]);
        }

        return [$rate->shipping_method_id, $rate->free_shipping_threshold && $subtotal >= (float) $rate->free_shipping_threshold ? 0.0 : (float) $rate->amount];
    }

    private function tax(ShippingAddress $address, float $taxableAmount): float
    {
        $rule = TaxRule::query()->where('is_active', true)->where('country', strtoupper($address->country))
            ->where(fn ($query) => $query->whereNull('state')->orWhere('state', $address->state))
            ->where(fn ($query) => $query->whereNull('postal_code')->orWhere('postal_code', $address->postal_code))
            ->orderByRaw('case when postal_code is null then 0 else 1 end desc')
            ->orderByRaw('case when state is null then 0 else 1 end desc')
            ->first();

        return $rule ? round($taxableAmount * ((float) $rule->rate / 100), 2) : 0.0;
    }
}
