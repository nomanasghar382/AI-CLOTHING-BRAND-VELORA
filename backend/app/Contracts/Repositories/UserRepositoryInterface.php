<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $attributes): User;

    public function update(User $user, array $attributes): User;

    public function findByEmail(string $email): ?User;
}
