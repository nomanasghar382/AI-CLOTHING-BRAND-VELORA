<?php

namespace App\Http\Controllers\Api\V1\Shopping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shopping\CheckoutRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ReturnRequest;
use App\Services\Shopping\CheckoutService;
use App\Services\Shopping\InvoiceService;
use App\Services\Shopping\StripePaymentService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class OrderController extends Controller
{
    use RespondsWithApi;

    public function __construct(
        private readonly CheckoutService $checkout,
        private readonly StripePaymentService $stripe,
        private readonly InvoiceService $invoices,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->success(OrderResource::collection($request->user()->orders()->with('items')->latest()->paginate(20)));
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return $this->success(new OrderResource($order->load(['items', 'shippingAddress', 'billingAddress', 'shippingMethod', 'history'])));
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        return $this->success(new OrderResource($this->checkout->checkout($request->user(), $request->validated())), 'Order created successfully.', 201);
    }

    public function requestReturn(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $attributes = $request->validate([
            'type' => ['required', 'in:return,refund'],
            'reason' => ['required', 'string', 'max:2000'],
            'requested_amount' => ['nullable', 'numeric', 'min:0.01', 'max:'.$order->grand_total],
        ]);
        if ($order->status !== 'delivered') {
            return $this->failure('Only delivered orders can be returned.', ['status' => ['Only delivered orders can be returned.']]);
        }
        if ($order->returnRequests()->whereIn('status', ['requested', 'approved', 'received'])->exists()) {
            return $this->failure('A return request is already open for this order.', ['return' => ['A return request is already open for this order.']]);
        }

        $return = ReturnRequest::query()->create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'type' => $attributes['type'],
            'reason' => $attributes['reason'],
            'requested_amount' => $attributes['requested_amount'] ?? $order->grand_total,
        ]);
        $order->update(['status' => 'return_requested']);
        OrderStatusHistory::query()->create(['order_id' => $order->id, 'user_id' => $request->user()->id, 'from_status' => 'delivered', 'to_status' => 'return_requested', 'note' => 'Customer requested a return.']);

        return $this->success(['id' => $return->id, 'status' => $return->status], 'Return request submitted.', 201);
    }

    public function paymentIntent(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        if ($order->payments()->where('provider', 'stripe')->doesntExist()) {
            return $this->failure('This order is not configured for Stripe payment.', [], 422);
        }
        $result = $this->stripe->createIntent($order);
        if (! $result['available']) {
            return $this->failure($result['reason'], [], 503);
        }

        return $this->success(['payment_id' => $result['payment']->id, 'client_secret' => $result['client_secret'], 'status' => $result['payment']->status], 'Stripe payment intent created successfully.');
    }

    public function invoice(Request $request, Order $order, string $format): Response
    {
        abort_unless($order->user_id === $request->user()->id || $request->user()->hasRole('admin', 'super-admin'), 404);
        abort_unless(in_array($format, ['html', 'pdf'], true), 404);

        return $this->invoices->download($order, $format);
    }
}
