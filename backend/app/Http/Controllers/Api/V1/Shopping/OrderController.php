<?php

namespace App\Http\Controllers\Api\V1\Shopping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shopping\CheckoutRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Services\Shopping\CheckoutService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OrderController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly CheckoutService $checkout) {}

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
}
