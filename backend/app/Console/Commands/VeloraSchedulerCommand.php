<?php

namespace App\Console\Commands;

use App\Models\ScheduledTaskRun;
use App\Services\Enterprise\BackupService;
use App\Services\Enterprise\CacheManagerService;
use App\Services\Enterprise\MetricsService;
use App\Services\Loyalty\ProductAlertService;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

final class VeloraSchedulerCommand extends Command
{
    protected $signature = 'velora:scheduler-run {task}';

    protected $description = 'Run a named Velora scheduled task with observability.';

    public function handle(
        BackupService $backups,
        CacheManagerService $cache,
        MetricsService $metrics,
        ProductAlertService $alerts,
    ): int {
        $task = $this->argument('task');
        $run = ScheduledTaskRun::query()->create([
            'command' => $task,
            'status' => 'running',
            'started_at' => now(),
        ]);
        $started = microtime(true);

        try {
            match ($task) {
                'exchange-rates' => $this->syncExchangeRates(),
                'trend-forecasts' => DB::table('bi_dashboard_snapshots')->count(),
                'analytics-snapshot' => $metrics->record('analytics.snapshot', 1, 'scheduler'),
                'inventory-scan' => $this->inventoryScan(),
                'low-stock-alerts' => $this->lowStockAlerts(),
                'expired-coupons' => DB::table('coupons')->where('expires_at', '<', now())->update(['is_active' => false]),
                'price-alerts' => $this->dispatchAlerts($alerts, 'price'),
                'restock-alerts' => $this->dispatchAlerts($alerts, 'restock'),
                'temp-cleanup' => $this->tempCleanup(),
                'database-cleanup' => $this->databaseCleanup(),
                'cache-warmup' => $cache->warmup(),
                'recommendation-refresh' => Artisan::call('queue:work', ['--once' => true]),
                'database-backup' => $backups->runDatabaseBackup(),
                'config-backup' => $backups->runConfigurationBackup(),
                'orphan-files' => Artisan::call('velora:scan-orphan-files'),
                default => throw new \InvalidArgumentException("Unknown task [{$task}]"),
            };

            $run->update([
                'status' => 'completed',
                'duration_ms' => (int) round((microtime(true) - $started) * 1000),
                'finished_at' => now(),
            ]);

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'duration_ms' => (int) round((microtime(true) - $started) * 1000),
                'error' => $exception->getMessage(),
                'finished_at' => now(),
            ]);

            return self::FAILURE;
        }
    }

    private function syncExchangeRates(): void
    {
        DB::table('currency_rates')->where('updated_at', '<', now()->subDay())->update(['updated_at' => now()]);
    }

    private function inventoryScan(): int
    {
        return Product::query()->where('stock_quantity', '<=', 0)->count();
    }

    private function lowStockAlerts(): int
    {
        return Product::query()->whereColumn('stock_quantity', '<=', 'minimum_stock')->count();
    }

    private function dispatchAlerts(ProductAlertService $alerts, string $type): int
    {
        $count = 0;
        Product::query()->where('status', 'published')->limit(25)->get()->each(function (Product $product) use ($alerts, &$count): void {
            $count += $alerts->dispatchEligible($product);
        });

        return $count;
    }

    private function tempCleanup(): int
    {
        $deleted = 0;
        foreach (glob(storage_path('app/tmp/*')) ?: [] as $file) {
            if (is_file($file) && filemtime($file) < now()->subDay()->timestamp) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    private function databaseCleanup(): int
    {
        return DB::table('application_metrics')
            ->where('created_at', '<', now()->subDays((int) config('velora.observability.metrics_retention_days', 30)))
            ->delete();
    }
}
