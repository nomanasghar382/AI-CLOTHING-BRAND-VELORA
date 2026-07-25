<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Models\CartItem;
use App\Models\ShoppingCart;
use App\Models\User;

final class EloquentCartRepository implements CartRepositoryInterface
{
    public function forUser(User $user): ShoppingCart
    {
        return ShoppingCart::query()->firstOrCreate(['user_id' => $user->id]);
    }

    public function item(ShoppingCart $cart, int $productId, ?int $variantId): ?CartItem
    {
        return CartItem::query()->where('shopping_cart_id', $cart->id)->where('product_id', $productId)
            ->where('product_variant_id', $variantId)->first();
    }
}
