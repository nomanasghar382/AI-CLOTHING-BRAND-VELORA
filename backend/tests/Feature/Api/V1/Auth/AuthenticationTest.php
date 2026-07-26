<?php

namespace Tests\Feature\Api\V1\Auth;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_with_standard_api_envelope(): void
    {
        $this->seed(RoleSeeder::class);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Amina Noor',
            'email' => 'amina@example.com',
            'password' => 'SecurePass!2026',
            'password_confirmation' => 'SecurePass!2026',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 201)
            ->assertJsonPath('data.email', 'amina@example.com')
            ->assertJsonPath('data.roles.0', 'customer');
    }

    public function test_authenticated_customer_can_read_profile(): void
    {
        $this->seed(RoleSeeder::class);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Amina Noor',
            'email' => 'amina@example.com',
            'password' => 'SecurePass!2026',
            'password_confirmation' => 'SecurePass!2026',
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'amina@example.com',
            'password' => 'SecurePass!2026',
        ]);

        $this->withToken($login->json('data.token'))
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'amina@example.com');
    }
}
