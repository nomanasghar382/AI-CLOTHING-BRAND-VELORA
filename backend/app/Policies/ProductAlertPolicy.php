<?php

namespace App\Policies;

use App\Models\ProductAlert;
use App\Models\User;

final class ProductAlertPolicy
{
    public function delete(User $user, ProductAlert $alert): bool
    {
        return $alert->user_id === $user->id || $user->hasRole('super-admin', 'admin');
    }
}
