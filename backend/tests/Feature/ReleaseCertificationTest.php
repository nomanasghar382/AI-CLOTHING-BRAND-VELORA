<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class ReleaseCertificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_release_version_is_1_0_0(): void
    {
        $this->assertSame('1.0.0', trim(File::get(base_path('../VERSION'))));
        $this->assertSame('1.0.0', config('velora.release.version'));
    }

    public function test_release_documentation_files_exist(): void
    {
        $docs = [
            'CHANGELOG.md',
            'RELEASE_NOTES.md',
            'VERSION',
            'README.md',
            'docs/API.md',
            'docs/DEPLOYMENT.md',
            'docs/DEVELOPER.md',
            'docs/MAINTENANCE.md',
            'docs/SECURITY.md',
            'docs/ARCHITECTURE.md',
            'docs/DATABASE.md',
            'docs/PROJECT_STRUCTURE.md',
            'docs/TESTING.md',
            'docs/USER_GUIDE.md',
            'docs/ADMIN_GUIDE.md',
            'docs/CREATOR_GUIDE.md',
            'docs/SUPPLIER_GUIDE.md',
            'docs/DEMO.md',
        ];

        foreach ($docs as $doc) {
            $this->assertFileExists(base_path('../'.$doc), "Missing documentation: {$doc}");
        }
    }

    public function test_core_api_routes_are_registered(): void
    {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(fn ($route) => $route->uri());

        foreach (['api/v1/products', 'api/v1/auth/login', 'api/v1/checkout', 'api/v1/admin/dashboard'] as $uri) {
            $this->assertTrue($routes->contains(fn ($registered) => str_contains($registered, $uri)), "Missing route: {$uri}");
        }
    }

    public function test_validate_release_command_passes_after_migrate(): void
    {
        $this->artisan('velora:validate-release', ['--migrate' => true])
            ->assertExitCode(0);
    }
}
