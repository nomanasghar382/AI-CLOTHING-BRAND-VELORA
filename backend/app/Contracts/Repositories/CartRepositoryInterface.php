<?php

namespace App\Contracts\Repositories;

use App\Models\CartItem;
use App\Models\ShoppingCart;
use App\Models\User;

interface CartRepositoryInterface
{
    public function forUser(User $user): ShoppingCart;

    public function item(ShoppingCart $cart, int $productId, ?int $variantId): ?CartItem;
}
