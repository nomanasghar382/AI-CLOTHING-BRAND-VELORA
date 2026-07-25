<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DemoEnvironmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ReleaseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_environment_seeder_populates_release_candidate_data(): void
    {
        config(['velora.demo_mode' => true]);
        $this->seed(RoleSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
        Product::factory()->count(3)->create(['status' => 'published']);
        $this->seed(DemoEnvironmentSeeder::class);

        $this->assertDatabaseHas('orders', ['number' => 'VEL-DEMO-0001']);
        $this->assertDatabaseHas('creator_profiles', ['handle' => 'velora-creator']);
        $this->assertDatabaseHas('supplier_profiles', ['slug' => 'velora-supply']);
        $this->assertDatabaseHas('community_posts', ['status' => 'published']);
        $this->assertDatabaseHas('wardrobe_items');
        $this->assertDatabaseHas('lookboards', ['title' => 'Weekend Layers']);
        $this->assertDatabaseHas('loyalty_transactions', ['idempotency_key' => 'demo-welcome-bonus']);
        $this->assertDatabaseHas('support_tickets', ['number' => 'SUP-DEMO-001']);
        $this->assertDatabaseHas('bi_forecasts', ['type' => 'sales']);
    }

    public function test_admin_workspace_search_and_release_validation_endpoints(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = $this->admin();
        $customer = User::factory()->create(['name' => 'Release Customer', 'email' => 'release-customer@velora.test']);
        $product = Product::factory()->create(['name' => 'Release Abaya', 'sku' => 'REL-001', 'status' => 'published']);
        Order::query()->create([
            'number' => 'VEL-RC-1001',
            'user_id' => $customer->id,
            'status' => 'processing',
            'payment_status' => 'paid',
            'currency' => 'USD',
            'subtotal' => 120,
            'discount_total' => 0,
            'shipping_total' => 10,
            'tax_total' => 8,
            'grand_total' => 138,
        ]);

        Sanctum::actingAs($admin);
        $this->getJson('/api/v1/admin/workspace')
            ->assertOk()
            ->assertJsonPath('data.quick_actions.0.id', 'add-product')
            ->assertJsonStructure(['data' => ['quick_actions', 'shortcuts', 'pinned_dashboards', 'recent_activity']]);

        $this->getJson('/api/v1/admin/search?q=Release')
            ->assertOk()
            ->assertJsonPath('data.query', 'Release')
            ->assertJsonStructure(['data' => ['results', 'groups', 'total']]);
    }

    public function test_guest_catalog_auth_and_shopping_workflow(): void
    {
        $this->seed(RoleSeeder::class);
        $product = Product::factory()->create(['status' => 'published', 'stock_quantity' => 5, 'price' => 89]);

        $this->getJson('/api/v1/products')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/products/'.$product->slug)->assertOk()->assertJsonPath('data.slug', $product->slug);

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 1])->assertCreated();
        $this->getJson('/api/v1/cart')->assertOk()->assertJsonCount(1, 'data.items');
        $this->getJson('/api/v1/loyalty/wallet')->assertOk();
        $this->getJson('/api/v1/notifications')->assertOk();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->where('slug', 'admin')->firstOrFail());

        return $admin;
    }
}
