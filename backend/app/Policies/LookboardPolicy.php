<?php

namespace App\Policies;

use App\Models\Lookboard;
use App\Models\User;

final class LookboardPolicy
{
    public function update(User $user, Lookboard $lookboard): bool
    {
        return $lookboard->user_id === $user->id;
    }

    public function delete(User $user, Lookboard $lookboard): bool
    {
        return $this->update($user, $lookboard);
    }
}
