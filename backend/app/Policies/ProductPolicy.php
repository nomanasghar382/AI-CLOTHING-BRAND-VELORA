<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super-admin', 'admin', 'creator', 'supplier');
    }

    public function view(User $user, Product $product): bool
    {
        return $this->ownsOrManages($user, $product);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Product $product): bool
    {
        return $this->ownsOrManages($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->ownsOrManages($user, $product);
    }

    private function ownsOrManages(User $user, Product $product): bool
    {
        return $user->hasRole('super-admin', 'admin') || $product->supplier_id === $user->id;
    }
}
