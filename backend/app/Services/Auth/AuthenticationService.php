<?php

namespace App\Services\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use App\Services\Enterprise\AuditService;
use App\Services\Enterprise\SecurityService;
use App\Services\Enterprise\StructuredLogService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AuthenticationService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly SecurityService $security,
        private readonly AuditService $audit,
        private readonly StructuredLogService $logs,
    ) {
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
        $this->audit->record('authentication', 'register', $user);
        $this->logs->auth('user.registered', ['user_id' => $user->id]);

        return $user->load('roles');
    }

    /**
     * @return array{user: User, token: string}
     */
    public function login(array $credentials, ?Request $request = null): array
    {
        $email = $credentials['email'];
        if ($request && $this->security->isSuspiciousLogin($email, $request)) {
            $this->security->recordLogin(null, $email, 'login.blocked', false, $request, true);
            $this->logs->security('login.blocked_suspicious', ['email' => $email, 'ip' => $request->ip()]);
            throw ValidationException::withMessages(['email' => ['Too many failed attempts. Please try again later.']]);
        }

        if (!Auth::attempt(['email' => $email, 'password' => $credentials['password']], (bool) ($credentials['remember'] ?? false))) {
            if ($request) {
                $this->security->recordLogin(null, $email, 'login.failed', false, $request);
            }
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => ['This account is inactive.']]);
        }

        if ($request) {
            $this->security->recordLogin($user, $email, 'login.success', true, $request);
            $this->security->registerDevice($user, $request, remembered: (bool) ($credentials['remember'] ?? false));
        }

        $token = $user->createToken($credentials['device_name'] ?? 'velora-web')->plainTextToken;
        $this->security->enforceConcurrentSessions($user);
        $this->audit->record('authentication', 'login.success', $user, request: $request);
        $this->logs->auth('user.login', ['user_id' => $user->id]);

        return ['user' => $user->load('roles'), 'token' => $token];
    }

    public function logout(User $user, ?Request $request = null): void
    {
        $user->currentAccessToken()?->delete();
        $this->audit->record('authentication', 'logout', $user, request: $request);
        $this->logs->auth('user.logout', ['user_id' => $user->id]);
    }

    public function rotatePassword(User $user, string $plainPassword): void
    {
        if (Hash::check($plainPassword, $user->password) || $this->security->passwordWasUsedRecently($user, $plainPassword)) {
            throw ValidationException::withMessages(['password' => ['You cannot reuse a recent password.']]);
        }

        $this->security->rememberPasswordHistory($user);
        $user->update(['password' => Hash::make($plainPassword)]);
        $user->tokens()->delete();
        $this->audit->record('authentication', 'password.rotated', $user);
    }
}
