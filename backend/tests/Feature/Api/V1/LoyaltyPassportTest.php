<?php

namespace Tests\Feature\Api\V1;

use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoyaltyPassportTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_manages_wallet_referral_preferences_and_alerts(): void
    {
        $customer = User::factory()->create();
        $referrer = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'price' => 100, 'stock_quantity' => 0]);
        Sanctum::actingAs($customer);

        $this->getJson('/api/v1/loyalty/wallet')->assertOk()->assertJsonPath('data.points_balance', 0);
        $code = $this->getJson('/api/v1/loyalty/referral-code')->assertOk()->json('data.code');
        $this->assertNotEmpty($code);
        $referrerCode = $this->actingAs($referrer)->getJson('/api/v1/loyalty/referral-code')->json('data.code');
        Sanctum::actingAs($customer);
        $this->postJson('/api/v1/loyalty/referrals', ['code' => $referrerCode])->assertCreated();
        $this->putJson('/api/v1/loyalty/preferences', ['price_alerts' => false])->assertOk()->assertJsonPath('data.price_alerts', false);
        $this->postJson('/api/v1/loyalty/alerts', ['product_id' => $product->id, 'type' => 'restock'])->assertCreated();
        $alert = $this->getJson('/api/v1/loyalty/alerts')->assertOk()->json('data.0.id');
        $this->deleteJson("/api/v1/loyalty/alerts/{$alert}")->assertOk();
    }

    public function test_verified_passport_is_database_sourced_and_admin_only_management(): void
    {
        $product = Product::factory()->create(['status' => 'published']);
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $this->getJson("/api/v1/products/{$product->id}/ethical-passport")->assertNotFound();

        $admin = User::factory()->create();
        $role = Role::query()->create(['name' => 'Admin', 'slug' => 'admin']);
        $admin->roles()->attach($role);
        Sanctum::actingAs($admin);
        $payload = ['product_id' => $product->id, 'status' => 'verified', 'verification_reference' => 'CERT-001', 'verified_by' => 'Independent verifier', 'verified_at' => now()->subDay()->toDateTimeString(), 'claims' => ['materials' => 'Organic cotton'], 'evidence' => [['category' => 'materials', 'title' => 'Material certificate', 'issuer' => 'Independent verifier', 'reference' => 'DOC-1', 'verified_at' => now()->subDay()->toDateTimeString()]]];
        $this->postJson('/api/v1/admin/ethical-passports', $payload)->assertForbidden();

        $permission = Permission::query()->create(['name' => 'Passports manage', 'slug' => 'passports.manage']);
        $role->permissions()->attach($permission);
        $this->postJson('/api/v1/admin/ethical-passports', $payload)->assertCreated()->assertJsonPath('data.verification_reference', 'CERT-001');
        Sanctum::actingAs($customer);
        $this->getJson("/api/v1/products/{$product->id}/ethical-passport")->assertOk()->assertJsonPath('data.claims.materials', 'Organic cotton');
    }

    public function test_admin_adjustment_is_idempotent_and_alert_dispatches_only_eligible_alerts(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'price' => 100, 'stock_quantity' => 2]);
        Sanctum::actingAs($customer);
        $this->postJson('/api/v1/loyalty/alerts', ['product_id' => $product->id, 'type' => 'price', 'target_price' => 100])->assertCreated();
        $admin = User::factory()->create();
        $role = Role::query()->create(['name' => 'Admin', 'slug' => 'admin']);
        $permission = Permission::query()->create(['name' => 'Loyalty manage', 'slug' => 'loyalty.manage']);
        $role->permissions()->attach($permission);
        $admin->roles()->attach($role);
        Sanctum::actingAs($admin);
        $payload = ['points_delta' => 100, 'credit_delta' => 5, 'idempotency_key' => 'audit-1'];
        $this->postJson("/api/v1/admin/loyalty/wallets/{$customer->id}/adjust", $payload)->assertOk()->assertJsonPath('data.points_balance', 100);
        $this->postJson("/api/v1/admin/loyalty/wallets/{$customer->id}/adjust", $payload)->assertOk()->assertJsonPath('data.points_balance', 100);
        $this->postJson("/api/v1/admin/loyalty/products/{$product->id}/dispatch-alerts")->assertOk()->assertJsonPath('data.notified', 1);
        $this->assertDatabaseHas('product_alerts', ['product_id' => $product->id, 'is_active' => false]);
    }
}
