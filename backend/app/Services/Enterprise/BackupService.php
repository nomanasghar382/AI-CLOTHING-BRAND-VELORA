<?php

namespace App\Services\Enterprise;

use App\Models\BackupRun;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class BackupService
{
    public function runDatabaseBackup(): BackupRun
    {
        $run = BackupRun::query()->create([
            'type' => 'database',
            'status' => 'running',
            'disk' => config('velora.backups.disk', 'local'),
            'retention_days' => (int) config('velora.backups.retention_days', 14),
        ]);

        try {
            $filename = 'db-'.now()->format('Ymd-His').'.sql';
            $path = trim(config('velora.backups.path', 'velora/backups'), '/').'/'.$filename;
            $absolute = storage_path('app/'.$path);

            if (! is_dir(dirname($absolute))) {
                mkdir(dirname($absolute), 0755, true);
            }

            if (config('database.default') === 'sqlite') {
                $source = database_path('database.sqlite');
                if (! copy($source, $absolute)) {
                    throw new RuntimeException('SQLite backup copy failed.');
                }
            } else {
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
                    escapeshellarg((string) config('database.connections.mysql.username')),
                    escapeshellarg((string) config('database.connections.mysql.password')),
                    escapeshellarg((string) config('database.connections.mysql.host')),
                    escapeshellarg((string) config('database.connections.mysql.port', 3306)),
                    escapeshellarg((string) config('database.connections.mysql.database')),
                    escapeshellarg($absolute)
                );
                exec($command, $output, $exitCode);
                if ($exitCode !== 0) {
                    throw new RuntimeException('Database dump failed.');
                }
            }

            $disk = Storage::disk(config('velora.backups.disk', 'local'));
            if ($disk->exists($path) === false && file_exists($absolute)) {
                $disk->put($path, file_get_contents($absolute));
            }

            $run->update([
                'status' => 'completed',
                'path' => $path,
                'size_bytes' => file_exists($absolute) ? filesize($absolute) : 0,
                'completed_at' => now(),
                'message' => 'Database backup completed.',
            ]);
        } catch (\Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }

        $this->applyRetention();

        return $run->fresh();
    }

    public function runConfigurationBackup(): BackupRun
    {
        $run = BackupRun::query()->create([
            'type' => 'configuration',
            'status' => 'running',
            'disk' => config('velora.backups.disk', 'local'),
            'retention_days' => (int) config('velora.backups.retention_days', 14),
        ]);

        try {
            $path = trim(config('velora.backups.path', 'velora/backups'), '/').'/config-'.now()->format('Ymd-His').'.json';
            $payload = [
                'app' => [
                    'name' => config('app.name'),
                    'url' => config('app.url'),
                    'environment' => app()->environment(),
                ],
                'velora' => config('velora'),
                'exported_at' => now()->toIso8601String(),
            ];
            Storage::disk(config('velora.backups.disk', 'local'))->put($path, json_encode($payload, JSON_PRETTY_PRINT));
            $run->update([
                'status' => 'completed',
                'path' => $path,
                'size_bytes' => strlen(json_encode($payload)),
                'completed_at' => now(),
                'message' => 'Configuration backup completed.',
            ]);
        } catch (\Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }

        return $run->fresh();
    }

    public function list(int $limit = 20): array
    {
        return BackupRun::query()->latest()->limit($limit)->get()->all();
    }

    private function applyRetention(): void
    {
        $days = (int) config('velora.backups.retention_days', 14);
        BackupRun::query()
            ->where('created_at', '<', now()->subDays($days))
            ->get()
            ->each(function (BackupRun $run): void {
                if ($run->path) {
                    Storage::disk($run->disk)->delete($run->path);
                }
                $run->delete();
            });
    }
}
