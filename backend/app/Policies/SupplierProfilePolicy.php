<?php

namespace App\Policies;

use App\Models\SupplierProfile;
use App\Models\User;

class SupplierProfilePolicy
{
    public function view(User $user, SupplierProfile $supplier): bool
    {
        return $user->hasRole('admin', 'super-admin') || $supplier->user_id === $user->id;
    }

    public function update(User $user, SupplierProfile $supplier): bool
    {
        return $this->view($user, $supplier);
    }

    public function manage(User $user): bool
    {
        return $user->hasRole('admin', 'super-admin');
    }
}
