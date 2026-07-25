<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\FeatureFlag;
use App\Models\User;
use Database\Seeders\FeatureFlagSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_operations_metrics(): void
    {
        $this->seed([RoleSeeder::class, UserSeeder::class]);
        Sanctum::actingAs(User::query()->where('email', 'admin@velora.test')->firstOrFail());

        $this->getJson('/api/v1/admin/operations/metrics')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['snapshot', 'recent']]);
    }

    public function test_admin_can_list_feature_flags(): void
    {
        $this->seed([RoleSeeder::class, UserSeeder::class, FeatureFlagSeeder::class]);
        Sanctum::actingAs(User::query()->where('email', 'admin@velora.test')->firstOrFail());

        $this->getJson('/api/v1/admin/operations/feature-flags')
            ->assertOk()
            ->assertJsonPath('success', true);

        $flag = FeatureFlag::query()->firstOrFail();
        $this->patchJson("/api/v1/admin/operations/feature-flags/{$flag->id}", ['enabled' => false])
            ->assertOk()
            ->assertJsonPath('data.enabled', false);
    }
}
