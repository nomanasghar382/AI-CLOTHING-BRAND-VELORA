<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CreatorProfileRepositoryInterface;
use App\Models\CreatorProfile;

final class EloquentCreatorProfileRepository implements CreatorProfileRepositoryInterface
{
    public function forUser(int $userId): ?CreatorProfile
    {
        return CreatorProfile::query()->where('user_id', $userId)->first();
    }

    public function save(int $userId, array $attributes): CreatorProfile
    {
        return CreatorProfile::query()->updateOrCreate(['user_id' => $userId], $attributes);
    }
}
