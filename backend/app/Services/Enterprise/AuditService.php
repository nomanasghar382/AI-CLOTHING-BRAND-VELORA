<?php

namespace App\Services\Enterprise;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class AuditService
{
    public function record(
        string $category,
        string $action,
        ?Model $subject = null,
        ?array $before = null,
        ?array $after = null,
        ?Request $request = null,
        array $metadata = [],
    ): AuditLog {
        return AuditLog::query()->create([
            'actor_id' => $request?->user()?->id,
            'category' => $category,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'before' => $before,
            'after' => $after,
            'metadata' => $metadata ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'request_id' => $request?->header('X-Request-Id') ?? (string) Str::uuid(),
        ]);
    }
}
