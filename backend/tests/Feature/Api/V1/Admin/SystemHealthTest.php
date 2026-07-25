<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SystemHealthTest extends TestCase
{
  use RefreshDatabase;

  public function test_admin_can_view_system_health(): void
  {
    $this->seed([RoleSeeder::class, UserSeeder::class]);
    $admin = User::query()->where('email', 'admin@velora.test')->firstOrFail();
    Sanctum::actingAs($admin);

    $this->getJson('/api/v1/admin/system/health')
      ->assertOk()
      ->assertJsonPath('success', true)
      ->assertJsonStructure(['data' => ['application', 'metrics']]);
  }
}
