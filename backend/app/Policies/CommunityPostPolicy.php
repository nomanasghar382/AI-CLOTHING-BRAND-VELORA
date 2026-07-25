<?php

namespace App\Policies;

use App\Models\CommunityPost;
use App\Models\User;

final class CommunityPostPolicy
{
    public function update(User $user, CommunityPost $post): bool
    {
        return $post->user_id === $user->id || $user->hasRole('super-admin', 'admin');
    }

    public function delete(User $user, CommunityPost $post): bool
    {
        return $this->update($user, $post);
    }

    public function moderate(User $user): bool
    {
        return $user->hasRole('super-admin', 'admin');
    }
}
