<?php

namespace App\Listeners;

use App\Services\Enterprise\AuditService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Events\Dispatcher;

final class RecordSecurityAudit
{
    public function __construct(private readonly AuditService $audit)
    {
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Login::class, [$this, 'handleLogin']);
        $events->listen(Logout::class, [$this, 'handleLogout']);
        $events->listen(Failed::class, [$this, 'handleFailed']);
        $events->listen(PasswordReset::class, [$this, 'handlePasswordReset']);
    }

    public function handleLogin(Login $event): void
    {
        $this->audit->record('authentication', 'login.success', $event->user, metadata: ['guard' => $event->guard]);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            $this->audit->record('authentication', 'logout', $event->user, metadata: ['guard' => $event->guard]);
        }
    }

    public function handleFailed(Failed $event): void
    {
        $this->audit->record('authentication', 'login.failed', metadata: [
            'email' => $event->credentials['email'] ?? null,
            'guard' => $event->guard,
        ]);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $this->audit->record('authentication', 'password.reset', $event->user);
    }
}
