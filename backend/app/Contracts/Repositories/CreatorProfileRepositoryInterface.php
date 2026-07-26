<?php

namespace App\Contracts\Repositories;

use App\Models\CreatorProfile;

interface CreatorProfileRepositoryInterface
{
    public function forUser(int $userId): ?CreatorProfile;

    public function save(int $userId, array $attributes): CreatorProfile;
}
