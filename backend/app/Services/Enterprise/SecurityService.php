<?php

namespace App\Services\Enterprise;

use App\Models\PasswordHistory;
use App\Models\SecurityLoginEvent;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class SecurityService
{
    public function fingerprint(Request $request): string
    {
        return hash('sha256', implode('|', [
            $request->userAgent() ?? 'unknown',
            $request->header('Accept-Language', 'en'),
            $request->ip() ?? '0.0.0.0',
        ]));
    }

    public function recordLogin(?User $user, string $email, string $event, bool $successful, Request $request, bool $suspicious = false): SecurityLoginEvent
    {
        return SecurityLoginEvent::query()->create([
            'user_id' => $user?->id,
            'email' => $email,
            'event' => $event,
            'successful' => $successful,
            'suspicious' => $suspicious,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_fingerprint' => $this->fingerprint($request),
            'metadata' => ['device_name' => $request->input('device_name')],
        ]);
    }

    public function isSuspiciousLogin(string $email, Request $request): bool
    {
        $threshold = (int) config('velora.security.suspicious_login_threshold', 5);
        $recentFailures = SecurityLoginEvent::query()
            ->where('email', $email)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        return $recentFailures >= $threshold;
    }

    public function registerDevice(User $user, Request $request, bool $trusted = false, bool $remembered = false): UserDevice
    {
        return UserDevice::query()->updateOrCreate(
            ['user_id' => $user->id, 'device_fingerprint' => $this->fingerprint($request)],
            [
                'device_name' => $request->input('device_name', 'Unknown device'),
                'platform' => $this->platform($request->userAgent()),
                'browser' => $this->browser($request->userAgent()),
                'ip_address' => $request->ip(),
                'trusted' => $trusted,
                'remembered' => $remembered,
                'last_seen_at' => now(),
                'revoked_at' => null,
            ]
        );
    }

    public function enforceConcurrentSessions(User $user): void
    {
        $max = (int) config('velora.security.max_concurrent_sessions', 5);
        $tokens = $user->tokens()->orderByDesc('last_used_at')->get();
        if ($tokens->count() <= $max) {
            return;
        }

        $tokens->slice($max)->each->delete();
    }

    public function rememberPasswordHistory(User $user): void
    {
        PasswordHistory::query()->create([
            'user_id' => $user->id,
            'password_hash' => $user->password,
            'rotated_at' => now(),
        ]);

        $keep = (int) config('velora.security.password_history_count', 5);
        $ids = PasswordHistory::query()
            ->where('user_id', $user->id)
            ->latest('rotated_at')
            ->skip($keep)
            ->pluck('id');

        if ($ids->isNotEmpty()) {
            PasswordHistory::query()->whereIn('id', $ids)->delete();
        }
    }

    public function passwordWasUsedRecently(User $user, string $plainPassword): bool
    {
        return PasswordHistory::query()
            ->where('user_id', $user->id)
            ->latest('rotated_at')
            ->limit((int) config('velora.security.password_history_count', 5))
            ->get()
            ->contains(fn (PasswordHistory $history) => Hash::check($plainPassword, $history->password_hash));
    }

    private function platform(?string $userAgent): ?string
    {
        if (! $userAgent) {
            return null;
        }

        return match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown',
        };
    }

    private function browser(?string $userAgent): ?string
    {
        if (! $userAgent) {
            return null;
        }

        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Unknown',
        };
    }
}
