<?php

namespace App\Http\Controllers\Api\V1\Shopping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shopping\CartItemRequest;
use App\Http\Requests\Shopping\UpdateCartItemRequest;
use App\Http\Resources\Api\V1\CartResource;
use App\Models\CartItem;
use App\Services\Shopping\CartService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CartController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly CartService $carts) {}

    public function show(Request $request): JsonResponse
    {
        return $this->success(new CartResource($this->carts->cart($request->user())));
    }

    public function store(CartItemRequest $request): JsonResponse
    {
        return $this->success(new CartResource($this->carts->add($request->user(), $request->validated())), 'Item added to cart.', 201);
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        return $this->success(new CartResource($this->carts->update($request->user(), $cartItem, $request->integer('quantity'))), 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        return $this->success(new CartResource($this->carts->remove($request->user(), $cartItem)), 'Item removed from cart.');
    }
}
