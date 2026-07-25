<?php

namespace App\Services\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AuthenticationService
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function register(array $attributes): User
    {
        $user = $this->users->create([
            ...$attributes,
            'password' => Hash::make($attributes['password']),
        ]);

        $customerRole = Role::query()->where('slug', 'customer')->firstOrFail();
        $user->roles()->syncWithoutDetaching([$customerRole->id]);

        event(new Registered($user));

        return $user->load('roles');
    }

    /**
     * @return array{user: User, token: string}
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], (bool) ($credentials['remember'] ?? false))) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => ['This account is inactive.']]);
        }

        $token = $user->createToken($credentials['device_name'] ?? 'velora-web')->plainTextToken;

        return ['user' => $user->load('roles'), 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
