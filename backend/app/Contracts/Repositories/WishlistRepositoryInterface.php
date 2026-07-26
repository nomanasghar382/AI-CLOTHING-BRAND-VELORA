<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;

interface WishlistRepositoryInterface
{
    public function forUser(User $user): Wishlist;

    public function item(Wishlist $wishlist, int $productId, ?int $variantId): ?WishlistItem;
}
