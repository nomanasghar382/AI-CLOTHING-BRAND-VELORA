<?php

namespace Tests\Feature\Api\V1;

use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupplierOperationsTest extends TestCase
{
    use RefreshDatabase;

    private function supplier(): User
    {
        $user = User::factory()->create(['name' => 'Acme Supplier']);
        $role = Role::query()->create(['name' => 'Supplier', 'slug' => 'supplier']);
        $user->roles()->attach($role);
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_supplier_manages_profile_inventory_operations_and_tokens(): void
    {
        $this->supplier();
        $product = Product::factory()->create();
        $this->putJson('/api/v1/supplier/profile', ['company_name' => 'Acme Supply', 'contact_email' => 'ops@acme.test'])->assertOk()->assertJsonPath('data.company_name', 'Acme Supply');
        $this->postJson('/api/v1/supplier/addresses', ['line1' => '1 Main', 'city' => 'Portland', 'postal_code' => '97201', 'country' => 'US'])->assertCreated();
        $this->getJson('/api/v1/supplier/addresses')->assertOk()->assertJsonPath('data.0.city', 'Portland');
        $this->postJson('/api/v1/supplier/inventory/sync', ['lines' => [['product_id' => $product->id, 'sku' => 'ACME-1', 'on_hand' => 10]]])->assertOk()->assertJsonPath('data.updated', 1);
        $inventory = $this->getJson('/api/v1/supplier/inventory')->assertOk()->json('data.data.0.id');
        $this->postJson("/api/v1/supplier/inventory/{$inventory}/reservations", ['quantity' => 3, 'reference' => 'order-1'])->assertCreated();
        $this->postJson("/api/v1/supplier/inventory/{$inventory}/adjustments", ['quantity_delta' => -2, 'reason' => 'cycle count', 'idempotency_key' => 'adj-1'])->assertOk()->assertJsonPath('data.available', 5);
        $this->postJson('/api/v1/supplier/purchase-orders', ['items' => [['sku' => 'ACME-1', 'quantity' => 4]], 'total' => 30])->assertCreated()->assertJsonPath('data.status', 'draft');
        $this->postJson('/api/v1/supplier/api-tokens', ['name' => 'Warehouse'])->assertCreated()->assertJsonStructure(['data' => ['token']]);
    }

    public function test_admin_bi_uses_historical_data(): void
    {
        $admin = User::factory()->create();
        $role = Role::query()->create(['name' => 'Admin', 'slug' => 'admin']);
        $admin->roles()->attach($role);
        Sanctum::actingAs($admin);
        $this->getJson('/api/v1/admin/bi/dashboard')->assertForbidden();
        Permission::query()->create(['name' => 'BI view', 'slug' => 'bi.view']);
        $role->permissions()->attach(Permission::query()->where('slug', 'bi.view')->value('id'));
        $this->postJson('/api/v1/analytics/events', ['type' => 'search', 'query' => 'linen'])->assertCreated();
        $this->getJson('/api/v1/admin/bi/analytics/search')->assertOk()->assertJsonPath('data.data.0.query', 'linen');
        $this->getJson('/api/v1/admin/bi/forecasts/sales?days=7')->assertOk()->assertJsonPath('data.method', 'historical_daily_average');
    }
}
