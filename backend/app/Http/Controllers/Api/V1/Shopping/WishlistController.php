<?php

namespace App\Http\Controllers\Api\V1\Shopping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shopping\WishlistItemRequest;
use App\Http\Resources\Api\V1\WishlistResource;
use App\Models\WishlistItem;
use App\Services\Shopping\WishlistService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WishlistController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly WishlistService $wishlists) {}

    public function show(Request $request): JsonResponse
    {
        return $this->success(new WishlistResource($this->wishlists->wishlist($request->user())));
    }

    public function store(WishlistItemRequest $request): JsonResponse
    {
        return $this->success(new WishlistResource($this->wishlists->add($request->user(), $request->validated())), 'Item added to wishlist.', 201);
    }

    public function destroy(Request $request, WishlistItem $wishlistItem): JsonResponse
    {
        return $this->success(new WishlistResource($this->wishlists->remove($request->user(), $wishlistItem)), 'Item removed from wishlist.');
    }
}
