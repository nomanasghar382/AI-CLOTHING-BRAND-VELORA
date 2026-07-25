<?php

namespace App\Policies;

use App\Models\CreatorCollection;
use App\Models\User;

final class CreatorCollectionPolicy
{
    public function update(User $user, CreatorCollection $collection): bool
    {
        return $collection->creator->user_id === $user->id || $user->hasRole('super-admin', 'admin');
    }

    public function delete(User $user, CreatorCollection $collection): bool
    {
        return $this->update($user, $collection);
    }
}
