<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_records_security_event_and_returns_security_headers(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create([
            'email' => 'secure@velora.test',
            'password' => Hash::make('VeloraDemo!2026'),
            'is_active' => true,
        ]);
        $user->roles()->attach(\App\Models\Role::query()->where('slug', 'customer')->value('id'));

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'secure@velora.test',
            'password' => 'VeloraDemo!2026',
            'device_name' => 'PHPUnit',
        ]);

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY');

        $this->assertDatabaseHas('security_login_events', [
            'email' => 'secure@velora.test',
            'event' => 'login.success',
            'successful' => true,
        ]);
    }

    public function test_password_rotation_rejects_recent_passwords(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create([
            'password' => Hash::make('VeloraDemo!2026'),
            'is_active' => true,
        ]);
        $user->roles()->attach(\App\Models\Role::query()->where('slug', 'customer')->value('id'));

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/auth/password', [
                'current_password' => 'VeloraDemo!2026',
                'password' => 'VeloraDemo!2026',
                'password_confirmation' => 'VeloraDemo!2026',
            ])
            ->assertStatus(422);
    }
}
