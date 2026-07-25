<?php

namespace App\Services\Admin;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

final class ActivityLogger
{
    public function log(Request $request, string $event, ?Model $subject = null, array $properties = []): void
    {
        ActivityLog::query()->create([
            'actor_id' => $request->user()?->id,
            'event' => $event,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
