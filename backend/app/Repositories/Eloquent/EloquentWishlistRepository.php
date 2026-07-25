<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\WishlistRepositoryInterface;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;

final class EloquentWishlistRepository implements WishlistRepositoryInterface
{
    public function forUser(User $user): Wishlist
    {
        return Wishlist::query()->firstOrCreate(['user_id' => $user->id]);
    }

    public function item(Wishlist $wishlist, int $productId, ?int $variantId): ?WishlistItem
    {
        return WishlistItem::query()->where('wishlist_id', $wishlist->id)->where('product_id', $productId)
            ->where('product_variant_id', $variantId)->first();
    }
}
