<?php

namespace App\Services\Enterprise;

use App\Models\ApplicationMetric;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class MetricsService
{
    public function record(string $metric, float $value, string $group = 'application', array $tags = []): void
    {
        ApplicationMetric::query()->create([
            'metric' => $metric,
            'group' => $group,
            'value' => $value,
            'tags' => $tags ?: null,
            'recorded_at' => now(),
        ]);
    }

    public function snapshot(): array
    {
        $queueSize = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        return [
            'requests' => [
                'slow_threshold_ms' => (int) config('velora.performance.slow_request_ms', 750),
            ],
            'database' => [
                'connected' => $this->databaseHealthy(),
                'slow_query_threshold_ms' => (int) config('velora.observability.slow_query_ms', 500),
            ],
            'queue' => [
                'connection' => config('queue.default'),
                'pending_jobs' => $queueSize,
                'failed_jobs' => $failedJobs,
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'healthy' => $this->cacheHealthy(),
            ],
            'memory' => [
                'usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ],
            'storage' => [
                'disk_free_mb' => round(disk_free_space(storage_path()) / 1024 / 1024, 2),
            ],
            'errors' => [
                'failed_jobs' => $failedJobs,
            ],
        ];
    }

    public function recent(string $group, int $limit = 50): array
    {
        return ApplicationMetric::query()
            ->where('group', $group)
            ->latest('recorded_at')
            ->limit($limit)
            ->get()
            ->map(fn (ApplicationMetric $metric) => [
                'metric' => $metric->metric,
                'value' => (float) $metric->value,
                'tags' => $metric->tags,
                'recorded_at' => $metric->recorded_at?->toIso8601String(),
            ])
            ->all();
    }

    private function databaseHealthy(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function cacheHealthy(): bool
    {
        try {
            Cache::put('velora.metrics.health', 'ok', 30);

            return Cache::get('velora.metrics.health') === 'ok';
        } catch (\Throwable) {
            return false;
        }
    }
}
