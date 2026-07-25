<?php

namespace App\Services\Shopping;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class CartService
{
    public function __construct(private readonly CartRepositoryInterface $carts) {}

    public function cart(User $user): ShoppingCart
    {
        return $this->carts->forUser($user)->load(['items.product', 'items.variant']);
    }

    public function add(User $user, array $attributes): ShoppingCart
    {
        $product = $this->purchasableProduct($attributes['product_id']);
        $variant = $this->variant($product, $attributes['product_variant_id'] ?? null);
        $cart = $this->carts->forUser($user);
        $item = $this->carts->item($cart, $product->id, $variant?->id);
        $quantity = ($item?->quantity ?? 0) + $attributes['quantity'];

        if ($quantity > $this->availableStock($product, $variant)) {
            throw ValidationException::withMessages(['quantity' => ['The requested quantity is unavailable.']]);
        }

        $price = $this->price($product, $variant);
        if ($item) {
            $item->update(['quantity' => $quantity, 'unit_price' => $price]);
        } else {
            CartItem::query()->create(['shopping_cart_id' => $cart->id, 'product_id' => $product->id, 'product_variant_id' => $variant?->id, 'quantity' => $quantity, 'unit_price' => $price]);
        }

        return $this->cart($user);
    }

    public function update(User $user, CartItem $item, int $quantity): ShoppingCart
    {
        $this->ownedItem($user, $item);
        $product = $this->purchasableProduct($item->product_id);
        $variant = $this->variant($product, $item->product_variant_id);
        if ($quantity > $this->availableStock($product, $variant)) {
            throw ValidationException::withMessages(['quantity' => ['The requested quantity is unavailable.']]);
        }
        $item->update(['quantity' => $quantity, 'unit_price' => $this->price($product, $variant)]);

        return $this->cart($user);
    }

    public function remove(User $user, CartItem $item): ShoppingCart
    {
        $this->ownedItem($user, $item);
        $item->delete();

        return $this->cart($user);
    }

    private function ownedItem(User $user, CartItem $item): void
    {
        if ($item->cart->user_id !== $user->id) {
            abort(404);
        }
    }

    private function purchasableProduct(int $id): Product
    {
        $product = Product::query()->findOrFail($id);
        if ($product->status !== 'published') {
            throw ValidationException::withMessages(['product_id' => ['This product is not available.']]);
        }

        return $product;
    }

    private function variant(Product $product, ?int $id): ?ProductVariant
    {
        if (! $id) {
            return null;
        }
        $variant = $product->variants()->find($id);
        if (! $variant || $variant->status !== 'active') {
            throw ValidationException::withMessages(['product_variant_id' => ['This variant is not available.']]);
        }

        return $variant;
    }

    private function price(Product $product, ?ProductVariant $variant): float
    {
        return round((float) ($product->sale_price ?? $product->price) + (float) ($variant?->price_difference ?? 0), 2);
    }

    private function availableStock(Product $product, ?ProductVariant $variant): int
    {
        return $variant?->stock_quantity ?? $product->stock_quantity;
    }
}
