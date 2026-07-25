<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RunDatabaseBackupJob;
use App\Models\AuditLog;
use App\Models\FeatureFlag;
use App\Models\IncomingWebhookEvent;
use App\Models\ScheduledTaskRun;
use App\Models\SecurityLoginEvent;
use App\Services\Enterprise\BackupService;
use App\Services\Enterprise\CacheManagerService;
use App\Services\Enterprise\MetricsService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

final class OperationsController extends Controller
{
    use RespondsWithApi;

    public function metrics(MetricsService $metrics): JsonResponse
    {
        return $this->success([
            'snapshot' => $metrics->snapshot(),
            'recent' => [
                'application' => $metrics->recent('application', 20),
                'database' => $metrics->recent('database', 20),
            ],
        ]);
    }

    public function queues(): JsonResponse
    {
        return $this->success([
            'connection' => config('queue.default'),
            'pending_jobs' => DB::table('jobs')->count(),
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'recent_failed' => DB::table('failed_jobs')->orderByDesc('failed_at')->limit(10)->get(),
        ]);
    }

    public function scheduler(): JsonResponse
    {
        return $this->success(ScheduledTaskRun::query()->latest()->limit(25)->get());
    }

    public function audits(Request $request): JsonResponse
    {
        $query = AuditLog::query()->with('actor:id,name,email')->latest();
        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        return $this->success($this->paginate($query, $request));
    }

    public function securityEvents(Request $request): JsonResponse
    {
        return $this->success($this->paginate(
            SecurityLoginEvent::query()->with('user:id,name,email')->latest(),
            $request
        ));
    }

    public function backups(BackupService $backups): JsonResponse
    {
        return $this->success($backups->list());
    }

    public function runBackup(): JsonResponse
    {
        RunDatabaseBackupJob::dispatch();

        return $this->success(null, 'Database backup queued.', 202);
    }

    public function featureFlags(): JsonResponse
    {
        return $this->success(FeatureFlag::query()->orderBy('key')->get());
    }

    public function updateFeatureFlag(Request $request, FeatureFlag $featureFlag): JsonResponse
    {
        $featureFlag->update($request->validate(['enabled' => ['required', 'boolean']]));

        return $this->success($featureFlag, 'Feature flag updated.');
    }

    public function webhooks(): JsonResponse
    {
        return $this->success(IncomingWebhookEvent::query()->latest()->limit(50)->get());
    }

    public function maintenance(Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            $enabled = $request->boolean('enabled');
            if ($enabled) {
                Artisan::call('down', ['--secret' => $request->string('secret', 'velora-maintenance')]);
            } else {
                Artisan::call('up');
            }
        }

        return $this->success([
            'maintenance_mode' => app()->isDownForMaintenance(),
        ]);
    }

    public function cache(CacheManagerService $cache, Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            $tag = $request->string('tag')->toString();
            $tag ? $cache->flushTag($tag) : $cache->flushAll();

            return $this->success(null, 'Cache operation completed.');
        }

        return $this->success([
            'driver' => config('cache.default'),
            'tags' => config('velora.cache.tags'),
        ]);
    }

    private function paginate($query, Request $request): array
    {
        $page = $query->paginate(min(max($request->integer('per_page', 20), 1), 100));

        return [
            'items' => $page->items(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ];
    }
}
