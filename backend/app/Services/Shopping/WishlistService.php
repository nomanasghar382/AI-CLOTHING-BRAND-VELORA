<?php

namespace App\Services\Shopping;

use App\Contracts\Repositories\WishlistRepositoryInterface;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Validation\ValidationException;

final class WishlistService
{
    public function __construct(private readonly WishlistRepositoryInterface $wishlists) {}

    public function wishlist(User $user): Wishlist
    {
        return $this->wishlists->forUser($user)->load(['items.product', 'items.variant']);
    }

    public function add(User $user, array $attributes): Wishlist
    {
        $product = Product::query()->findOrFail($attributes['product_id']);
        $variantId = $attributes['product_variant_id'] ?? null;
        if ($variantId && ! $product->variants()->whereKey($variantId)->exists()) {
            throw ValidationException::withMessages(['product_variant_id' => ['The variant does not belong to the product.']]);
        }
        $wishlist = $this->wishlists->forUser($user);
        WishlistItem::query()->firstOrCreate(['wishlist_id' => $wishlist->id, 'product_id' => $product->id, 'product_variant_id' => $variantId]);

        return $this->wishlist($user);
    }

    public function remove(User $user, WishlistItem $item): Wishlist
    {
        if ($item->wishlist->user_id !== $user->id) {
            abort(404);
        }
        $item->delete();

        return $this->wishlist($user);
    }
}
