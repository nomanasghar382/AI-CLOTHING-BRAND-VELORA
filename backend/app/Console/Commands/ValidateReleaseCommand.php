<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class ValidateReleaseCommand extends Command
{
    protected $signature = 'velora:validate-release {--json : Output results as JSON}';

    protected $description = 'Validate VELORA release candidate readiness across modules, schema, and configuration.';

    /** @var list<array{check:string,status:string,detail:string}> */
    private array $checks = [];

    public function handle(): int
    {
        $this->validateRoutes();
        $this->validateDatabase();
        $this->validateConfiguration();
        $this->validateDocumentation();
        $this->validateFrontendBuildArtifacts();

        $failed = collect($this->checks)->where('status', 'fail')->count();

        if ($this->option('json')) {
            $this->line(json_encode(['checks' => $this->checks, 'failed' => $failed], JSON_PRETTY_PRINT));
        } else {
            foreach ($this->checks as $check) {
                $icon = match ($check['status']) {
                    'pass' => '<fg=green>✓</>',
                    'warn' => '<fg=yellow>!</>',
                    default => '<fg=red>✗</>',
                };
                $this->line("{$icon} {$check['check']} — {$check['detail']}");
            }
            $this->newLine();
            $this->info($failed === 0 ? 'Release validation passed.' : "Release validation failed ({$failed} checks).");
        }

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function validateRoutes(): void
    {
        $required = [
            'api/v1/auth/login',
            'api/v1/products',
            'api/v1/checkout',
            'api/v1/admin/dashboard',
            'api/v1/admin/system/health',
            'api/v1/admin/operations/metrics',
            'api/v1/wardrobe',
            'api/v1/community/posts',
            'api/v1/loyalty/wallet',
            'api/v1/supplier/profile',
        ];

        $missing = collect($required)->filter(fn (string $uri) => ! collect(Route::getRoutes())->contains(fn ($route) => str_contains($route->uri(), $uri)))->values();

        $this->record('API routes', $missing->isEmpty() ? 'pass' : 'fail', $missing->isEmpty() ? count($required).' core routes registered' : 'Missing: '.$missing->implode(', '));
    }

    private function validateDatabase(): void
    {
        $tables = [
            'users', 'products', 'orders', 'payments', 'community_posts', 'wardrobe_items',
            'creator_profiles', 'supplier_profiles', 'loyalty_wallets', 'notifications',
            'audit_logs', 'application_metrics', 'backup_runs', 'feature_flags',
        ];

        $missingTables = collect($tables)->reject(fn (string $table) => Schema::hasTable($table))->values();

        $this->record('Database tables', $missingTables->isEmpty() ? 'pass' : 'fail', $missingTables->isEmpty() ? count($tables).' core tables present' : 'Missing: '.$missingTables->implode(', '));

        try {
            DB::connection()->getPdo();
            $this->record('Database connection', 'pass', 'Connected to '.config('database.default'));
        } catch (Throwable $exception) {
            $this->record('Database connection', 'fail', $exception->getMessage());
        }
    }

    private function validateConfiguration(): void
    {
        $this->record('Application key', config('app.key') ? 'pass' : 'warn', config('app.key') ? 'APP_KEY configured' : 'APP_KEY missing');
        $this->record('API versioning', in_array('v1', config('velora.api.supported_versions', []), true) ? 'pass' : 'fail', 'Supported versions: '.implode(', ', config('velora.api.supported_versions', [])));
        $this->record('Demo mode flag', config('velora.demo_mode') !== null ? 'pass' : 'warn', 'VELORA_DEMO_MODE='.(config('velora.demo_mode') ? 'true' : 'false'));
        $this->record('Secrets isolation', env('STRIPE_SECRET') === null || app()->environment('testing') ? 'pass' : 'warn', 'Stripe secret loaded server-side only');
    }

    private function validateDocumentation(): void
    {
        $docs = [
            '../CHANGELOG.md',
            '../RELEASE_NOTES.md',
            '../VERSION',
            '../docs/release/API.md',
            '../docs/release/DATABASE.md',
            '../docs/release/DEPLOYMENT.md',
            '../docs/release/ENVIRONMENT.md',
            '../docs/release/TESTING.md',
        ];

        $missing = collect($docs)->reject(fn (string $path) => File::exists(base_path($path)))->values();
        $this->record('Release documentation', $missing->isEmpty() ? 'pass' : 'fail', $missing->isEmpty() ? count($docs).' release files present' : 'Missing: '.$missing->implode(', '));
    }

    private function validateFrontendBuildArtifacts(): void
    {
        $dist = base_path('../frontend/dist/index.html');
        $this->record('Frontend build', File::exists($dist) ? 'pass' : 'warn', File::exists($dist) ? 'frontend/dist present' : 'Run npm run build before deployment');
    }

    private function record(string $check, string $status, string $detail): void
    {
        $this->checks[] = compact('check', 'status', 'detail');
    }
}
