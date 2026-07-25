<?php

namespace Tests\Feature;

use App\Models\GiftCard;
use App\Models\Role;
use App\Models\User;
use App\Services\Loyalty\LoyaltyService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class EngineeringAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_update_requires_read_boolean(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('slug', 'customer')->firstOrFail());
        Sanctum::actingAs($user);

        $this->patchJson('/api/v1/notifications/fake-id', [])->assertUnprocessable()->assertJsonValidationErrors(['read']);
    }

    public function test_supplier_cannot_attach_shipment_to_foreign_purchase_order(): void
    {
        $supplierA = $this->supplierUser('Supplier A');
        $supplierB = $this->supplierUser('Supplier B');
        Sanctum::actingAs($supplierB);
        $this->putJson('/api/v1/supplier/profile', ['company_name' => 'Supplier B Co'])->assertOk();
        $foreignPoId = $this->postJson('/api/v1/supplier/purchase-orders', ['items' => [['sku' => 'SKU-1', 'quantity' => 1]], 'total' => 10])
            ->assertCreated()
            ->json('data.id');

        Sanctum::actingAs($supplierA);
        $this->postJson('/api/v1/supplier/shipments', ['purchase_order_id' => $foreignPoId, 'tracking_number' => 'TRACK-1'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['purchase_order_id']);
    }

    public function test_supplier_token_abilities_are_restricted(): void
    {
        $supplier = $this->supplierUser('Token Supplier');
        Sanctum::actingAs($supplier);

        $this->postJson('/api/v1/supplier/api-tokens', ['name' => 'Integration', 'abilities' => ['*']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['abilities.0']);
    }

    public function test_gift_card_purchase_requires_wallet_balance(): void
    {
        $this->seed(RoleSeeder::class);
        $customer = User::factory()->create();
        $customer->roles()->attach(Role::query()->where('slug', 'customer')->firstOrFail());
        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/loyalty/gift-cards', ['amount' => 100])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_gift_card_purchase_debits_wallet_balance(): void
    {
        $this->seed(RoleSeeder::class);
        $customer = User::factory()->create();
        $customer->roles()->attach(Role::query()->where('slug', 'customer')->firstOrFail());
        $wallet = app(LoyaltyService::class)->wallet($customer);
        $wallet->update(['store_credit_balance' => 150]);
        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/loyalty/gift-cards', ['amount' => 50])
            ->assertCreated()
            ->assertJsonPath('data.balance', '50.00');

        $this->assertDatabaseHas('loyalty_wallets', ['user_id' => $customer->id, 'store_credit_balance' => '100.00']);
        $this->assertEquals(1, GiftCard::query()->count());
    }

    public function test_admin_customer_update_rejects_non_customer_accounts(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $adminRole = Role::query()->where('slug', 'admin')->firstOrFail();
        $permission = \App\Models\Permission::query()->firstOrCreate(['slug' => 'customers.manage'], ['name' => 'Manage customers']);
        $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
        $admin->roles()->attach($adminRole);
        $supplier = User::factory()->create();
        $supplier->roles()->attach(Role::query()->where('slug', 'supplier')->firstOrFail());
        Sanctum::actingAs($admin);

        $this->patchJson("/api/v1/admin/customers/{$supplier->id}", ['is_active' => false])->assertNotFound();
    }

    private function supplierUser(string $name): User
    {
        $user = User::factory()->create(['name' => $name]);
        $user->roles()->attach(Role::query()->firstOrCreate(['slug' => 'supplier'], ['name' => 'Supplier']));

        return $user;
    }
}
