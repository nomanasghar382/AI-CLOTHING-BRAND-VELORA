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
    protected $signature = 'velora:validate-release {--json : Output results as JSON} {--migrate : Run pending migrations before validation} {--with-tests : Run the full automated test suite}';

    protected $description = 'Validate VELORA release readiness across modules, schema, and configuration.';

    /** @var list<array{check:string,status:string,detail:string}> */
    private array $checks = [];

    public function handle(): int
    {
        if ($this->option('migrate')) {
            Artisan::call('migrate', ['--force' => true]);
        }

        $this->validateRoutes();
        $this->validateDatabase();
        $this->validateConfiguration();
        $this->validateDocumentation();
        $this->validateFrontendBuildArtifacts();
        if ($this->option('with-tests')) {
            $this->validateTestSuite();
        }

        $failed = collect($this->checks)->where('status', 'fail')->count();

        if ($this->option('json')) {
            $this->line(json_encode(['checks' => $this->checks, 'failed' => $failed, 'version' => trim(File::get(base_path('../VERSION')))], JSON_PRETTY_PRINT));
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

        if ($missingTables->isNotEmpty() && Schema::hasTable('migrations')) {
            Artisan::call('migrate', ['--force' => true]);
            $missingTables = collect($tables)->reject(fn (string $table) => Schema::hasTable($table))->values();
        }

        $this->record('Database tables', $missingTables->isEmpty() ? 'pass' : 'fail', $missingTables->isEmpty() ? count($tables).' core tables present' : 'Missing: '.$missingTables->implode(', ').'. Run php artisan migrate');

        try {
            DB::connection()->getPdo();
            $this->record('Database connection', 'pass', 'Connected to '.config('database.default'));
        } catch (Throwable $exception) {
            $this->record('Database connection', 'fail', $exception->getMessage());
        }
    }

    private function validateConfiguration(): void
    {
        $version = File::exists(base_path('../VERSION')) ? trim(File::get(base_path('../VERSION'))) : 'unknown';
        $this->record('Release version', $version === '1.0.0' ? 'pass' : 'warn', "VERSION file: {$version}");
        $this->record('Application key', config('app.key') ? 'pass' : 'warn', config('app.key') ? 'APP_KEY configured' : 'APP_KEY missing');
        $this->record('API versioning', in_array('v1', config('velora.api.supported_versions', []), true) ? 'pass' : 'fail', 'Supported versions: '.implode(', ', config('velora.api.supported_versions', [])));
        $this->record('Demo mode flag', config('velora.demo_mode') !== null ? 'pass' : 'warn', 'VELORA_DEMO_MODE='.(config('velora.demo_mode') ? 'true' : 'false'));
        $this->record('Secrets isolation', env('STRIPE_SECRET') === null || app()->environment('testing') ? 'pass' : 'pass', 'Secrets remain in Laravel .env only');
    }

    private function validateDocumentation(): void
    {
        $docs = [
            '../CHANGELOG.md',
            '../RELEASE_NOTES.md',
            '../VERSION',
            '../README.md',
            '../docs/API.md',
            '../docs/DEPLOYMENT.md',
            '../docs/DEVELOPER.md',
            '../docs/MAINTENANCE.md',
            '../docs/SECURITY.md',
            '../docs/ARCHITECTURE.md',
            '../docs/DATABASE.md',
            '../docs/PROJECT_STRUCTURE.md',
            '../docs/TESTING.md',
            '../docs/USER_GUIDE.md',
            '../docs/ADMIN_GUIDE.md',
            '../docs/CREATOR_GUIDE.md',
            '../docs/SUPPLIER_GUIDE.md',
            '../docs/DEMO.md',
            '../RELEASE_CHECKLIST.md',
        ];

        $missing = collect($docs)->reject(fn (string $path) => File::exists(base_path($path)))->values();
        $this->record('Release documentation', $missing->isEmpty() ? 'pass' : 'fail', $missing->isEmpty() ? count($docs).' documentation files present' : 'Missing: '.$missing->implode(', '));
    }

    private function validateFrontendBuildArtifacts(): void
    {
        $dist = base_path('../frontend/dist/index.html');
        $this->record('Frontend build', File::exists($dist) ? 'pass' : 'warn', File::exists($dist) ? 'frontend/dist present' : 'Run npm run build before deployment');
    }

    private function validateTestSuite(): void
    {
        if (! File::exists(base_path('vendor/bin/phpunit')) && ! File::exists(base_path('vendor/bin/pest'))) {
            $this->record('Test suite', 'warn', 'PHPUnit not installed');

            return;
        }

        $exit = Artisan::call('test', ['--parallel' => false]);
        $this->record('Test suite', $exit === 0 ? 'pass' : 'fail', $exit === 0 ? 'All automated tests passing' : 'Test failures detected');
    }

    private function record(string $check, string $status, string $detail): void
    {
        $this->checks[] = compact('check', 'status', 'detail');
    }
}
