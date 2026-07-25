<?php

namespace App\Http\Controllers\Api\V1\Shopping;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CouponResource;
use App\Http\Resources\Api\V1\OrderResource;
use App\Http\Resources\Api\V1\PaymentResource;
use App\Http\Resources\Api\V1\ReturnRequestResource;
use App\Http\Resources\Api\V1\ShippingMethodResource;
use App\Http\Resources\Api\V1\TaxRuleResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\ReturnRequest;
use App\Models\ShippingMethod;
use App\Models\TaxRule;
use App\Notifications\OrderStatusNotification;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminShoppingController extends Controller
{
    use RespondsWithApi;

    private const TRANSITIONS = [
        'pending' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => ['return_requested'],
        'return_requested' => ['returned'],
    ];

    public function orders(Request $request): JsonResponse
    {
        $query = Order::query()->with(['user', 'items', 'shippingMethod'])->latest();
        $this->scopeOrders($query, $request);
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')))
            ->when($request->filled('search'), fn ($q) => $q->where('number', 'like', '%'.$request->string('search').'%'));

        return $this->success($this->paginated($query->paginate($request->integer('per_page', 20)), OrderResource::class, $request));
    }

    public function order(Request $request, Order $order): JsonResponse
    {
        $this->ensureOrderAccess($request, $order);

        return $this->success(new OrderResource($order->load(['user', 'items', 'shippingAddress', 'billingAddress', 'shippingMethod', 'history', 'payments.transactions', 'returnRequests.user'])));
    }

    public function transition(Request $request, Order $order): JsonResponse
    {
        $this->ensureOrderAccess($request, $order);
        $attributes = $request->validate(['status' => ['required', 'string', Rule::in(array_merge(array_keys(self::TRANSITIONS), ...array_values(self::TRANSITIONS)))], 'note' => ['nullable', 'string', 'max:2000']]);
        if (! in_array($attributes['status'], self::TRANSITIONS[$order->status] ?? [], true)) {
            throw ValidationException::withMessages(['status' => ["An order cannot move from {$order->status} to {$attributes['status']}."]]);
        }

        DB::transaction(function () use ($request, $order, $attributes): void {
            $from = $order->status;
            $order->update(['status' => $attributes['status']]);
            OrderStatusHistory::query()->create(['order_id' => $order->id, 'user_id' => $request->user()->id, 'from_status' => $from, 'to_status' => $attributes['status'], 'note' => $attributes['note'] ?? null]);
            if (in_array($attributes['status'], ['shipped', 'delivered', 'cancelled'], true)) {
                DB::afterCommit(fn () => $order->user->notify(new OrderStatusNotification($order, $attributes['status'])));
            }
        });

        return $this->success(new OrderResource($order->fresh()->load(['user', 'items', 'history', 'payments.transactions', 'returnRequests.user'])), 'Order status updated.');
    }

    public function coupons(Request $request): JsonResponse
    {
        return $this->success($this->paginated(Coupon::query()->latest()->paginate($request->integer('per_page', 20)), CouponResource::class, $request));
    }

    public function storeCoupon(Request $request): JsonResponse
    {
        return $this->success(new CouponResource(Coupon::query()->create($this->couponData($request))), 'Coupon created.', 201);
    }

    public function updateCoupon(Request $request, Coupon $coupon): JsonResponse
    {
        $coupon->update($this->couponData($request, $coupon));

        return $this->success(new CouponResource($coupon), 'Coupon updated.');
    }

    public function destroyCoupon(Coupon $coupon): JsonResponse
    {
        $coupon->delete();

        return $this->success(null, 'Coupon deleted.');
    }

    public function shippingMethods(Request $request): JsonResponse
    {
        return $this->success($this->paginated(ShippingMethod::query()->with('rates')->latest()->paginate($request->integer('per_page', 20)), ShippingMethodResource::class, $request));
    }

    public function storeShippingMethod(Request $request): JsonResponse
    {
        $data = $this->shippingData($request);
        $method = ShippingMethod::query()->create(collect($data)->except('rates')->all());
        $this->syncRates($method, $data['rates'] ?? []);

        return $this->success(new ShippingMethodResource($method->load('rates')), 'Shipping method created.', 201);
    }

    public function updateShippingMethod(Request $request, ShippingMethod $shippingMethod): JsonResponse
    {
        $data = $this->shippingData($request, $shippingMethod);
        $shippingMethod->update(collect($data)->except('rates')->all());
        if (array_key_exists('rates', $data)) {
            $this->syncRates($shippingMethod, $data['rates']);
        }

        return $this->success(new ShippingMethodResource($shippingMethod->load('rates')), 'Shipping method updated.');
    }

    public function destroyShippingMethod(ShippingMethod $shippingMethod): JsonResponse
    {
        $shippingMethod->delete();

        return $this->success(null, 'Shipping method deleted.');
    }

    public function taxRules(Request $request): JsonResponse
    {
        return $this->success($this->paginated(TaxRule::query()->latest()->paginate($request->integer('per_page', 20)), TaxRuleResource::class, $request));
    }

    public function storeTaxRule(Request $request): JsonResponse
    {
        return $this->success(new TaxRuleResource(TaxRule::query()->create($this->taxData($request))), 'Tax rule created.', 201);
    }

    public function updateTaxRule(Request $request, TaxRule $taxRule): JsonResponse
    {
        $taxRule->update($this->taxData($request));

        return $this->success(new TaxRuleResource($taxRule), 'Tax rule updated.');
    }

    public function destroyTaxRule(TaxRule $taxRule): JsonResponse
    {
        $taxRule->delete();

        return $this->success(null, 'Tax rule deleted.');
    }

    public function payments(Request $request): JsonResponse
    {
        return $this->success($this->paginated(Payment::query()->with(['order', 'transactions'])->latest()->paginate($request->integer('per_page', 20)), PaymentResource::class, $request));
    }

    public function capturePayment(Payment $payment): JsonResponse
    {
        if ($payment->status !== 'pending') {
            throw ValidationException::withMessages(['payment' => ['Only pending payments can be captured.']]);
        }
        DB::transaction(function () use ($payment): void {
            $payment->update(['status' => 'paid']);
            $payment->order->update(['payment_status' => 'paid']);
            $payment->transactions()->create(['type' => 'capture', 'status' => 'succeeded', 'amount' => $payment->amount]);
            DB::afterCommit(fn () => $payment->order->user->notify(new OrderStatusNotification($payment->order, 'payment')));
        });

        return $this->success(new PaymentResource($payment->fresh()->load(['order', 'transactions'])), 'Payment captured.');
    }

    public function refundPayment(Request $request, Payment $payment): JsonResponse
    {
        $attributes = $request->validate(['amount' => ['required', 'numeric', 'gt:0']]);
        if ($payment->status !== 'paid') {
            throw ValidationException::withMessages(['payment' => ['Only paid payments can be refunded.']]);
        }
        $alreadyRefunded = (float) $payment->transactions()->where('type', 'refund')->where('status', 'succeeded')->sum('amount');
        if ((float) $attributes['amount'] > (float) $payment->amount - $alreadyRefunded) {
            throw ValidationException::withMessages(['amount' => ['Refund exceeds the captured amount.']]);
        }
        DB::transaction(function () use ($payment, $attributes, $alreadyRefunded): void {
            $payment->transactions()->create(['type' => 'refund', 'status' => 'succeeded', 'amount' => $attributes['amount']]);
            if ($alreadyRefunded + (float) $attributes['amount'] >= (float) $payment->amount) {
                $payment->update(['status' => 'refunded']);
                $payment->order->update(['payment_status' => 'refunded']);
                DB::afterCommit(fn () => $payment->order->user->notify(new OrderStatusNotification($payment->order, 'refunded')));
            }
        });

        return $this->success(new PaymentResource($payment->fresh()->load(['order', 'transactions'])), 'Refund recorded.');
    }

    public function returns(Request $request): JsonResponse
    {
        return $this->success($this->paginated(ReturnRequest::query()->with(['order', 'user'])->latest()->paginate($request->integer('per_page', 20)), ReturnRequestResource::class, $request));
    }

    public function updateReturn(Request $request, ReturnRequest $returnRequest): JsonResponse
    {
        $attributes = $request->validate(['status' => ['required', Rule::in(['approved', 'rejected', 'received', 'refunded'])], 'admin_note' => ['nullable', 'string', 'max:2000'], 'refund_amount' => ['required_if:status,refunded', 'nullable', 'numeric', 'min:0.01']]);
        $returnRequest->update([...$attributes, 'reviewed_by' => $request->user()->id, 'resolved_at' => now()]);
        if ($attributes['status'] === 'received' && $returnRequest->order->status === 'return_requested') {
            $returnRequest->order->update(['status' => 'returned']);
            OrderStatusHistory::query()->create(['order_id' => $returnRequest->order_id, 'user_id' => $request->user()->id, 'from_status' => 'return_requested', 'to_status' => 'returned', 'note' => $attributes['admin_note'] ?? null]);
        }
        if ($attributes['status'] === 'refunded') {
            $payment = $returnRequest->order->payments()->where('status', 'paid')->latest()->first();
            if (! $payment) {
                throw ValidationException::withMessages(['payment' => ['A paid payment is required before a refund can be recorded.']]);
            }
            $payment->transactions()->create(['type' => 'refund', 'status' => 'succeeded', 'amount' => $attributes['refund_amount']]);
            $payment->update(['status' => 'refunded']);
            $returnRequest->order->update(['payment_status' => 'refunded', 'status' => 'refunded']);
            $returnRequest->order->user->notify(new OrderStatusNotification($returnRequest->order, 'refunded'));
        }

        return $this->success(new ReturnRequestResource($returnRequest->fresh()->load(['order', 'user'])), 'Return request updated.');
    }

    private function scopeOrders($query, Request $request): void
    {
        if (! $request->user()->hasRole('super-admin', 'admin')) {
            $query->whereHas('items.product', fn ($q) => $q->where('supplier_id', $request->user()->id))
                ->whereDoesntHave('items.product', fn ($q) => $q->where('supplier_id', '!=', $request->user()->id));
        }
    }

    private function ensureOrderAccess(Request $request, Order $order): void
    {
        $query = Order::query()->whereKey($order->id);
        $this->scopeOrders($query, $request);
        abort_unless($query->exists(), 404);
    }

    private function couponData(Request $request, ?Coupon $coupon = null): array
    {
        return $request->validate(['code' => ['required', 'string', 'max:64', Rule::unique('coupons', 'code')->ignore($coupon)], 'type' => ['required', Rule::in(['percentage', 'fixed'])], 'value' => ['required', 'numeric', 'gt:0'], 'minimum_order_amount' => ['nullable', 'numeric', 'min:0'], 'maximum_discount_amount' => ['nullable', 'numeric', 'min:0'], 'usage_limit' => ['nullable', 'integer', 'min:1'], 'starts_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date', 'after:starts_at'], 'is_active' => ['required', 'boolean']]);
    }

    private function shippingData(Request $request, ?ShippingMethod $method = null): array
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'code' => ['required', 'alpha_dash', 'max:64', Rule::unique('shipping_methods', 'code')->ignore($method)], 'description' => ['nullable', 'string'], 'is_active' => ['required', 'boolean'], 'rates' => ['sometimes', 'array'], 'rates.*.id' => ['nullable', 'integer', 'exists:shipping_rates,id'], 'rates.*.country' => ['nullable', 'string', 'size:2'], 'rates.*.amount' => ['required_with:rates', 'numeric', 'min:0'], 'rates.*.free_shipping_threshold' => ['nullable', 'numeric', 'min:0'], 'rates.*.is_active' => ['required_with:rates', 'boolean']]);

        return $data;
    }

    private function taxData(Request $request): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:255'], 'country' => ['required', 'string', 'size:2'], 'state' => ['nullable', 'string', 'max:255'], 'postal_code' => ['nullable', 'string', 'max:32'], 'rate' => ['required', 'numeric', 'min:0', 'max:100'], 'is_active' => ['required', 'boolean']]);
    }

    private function syncRates(ShippingMethod $method, array $rates): void
    {
        DB::transaction(function () use ($method, $rates): void {
            $existing = $method->rates()->pluck('id')->all();
            $seen = [];
            foreach ($rates as $rate) {
                $id = $rate['id'] ?? null;
                if ($id && ! in_array($id, $existing, true)) {
                    throw ValidationException::withMessages(['rates' => ['A rate does not belong to this shipping method.']]);
                }
                $model = $id ? $method->rates()->findOrFail($id) : $method->rates()->make();
                $model->fill(collect($rate)->except('id')->all())->save();
                $seen[] = $model->id;
            }
            $method->rates()->whereNotIn('id', $seen)->delete();
        });
    }

    private function paginated($paginator, string $resource, Request $request): array
    {
        return [
            'data' => $resource::collection($paginator->items())->resolve($request),
            'meta' => [
                'current_page' => $paginator->currentPage(), 'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(), 'total' => $paginator->total(),
            ],
        ];
    }
}
