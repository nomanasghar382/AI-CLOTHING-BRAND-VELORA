<?php

namespace Tests\Feature\Api\V1\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_create_a_product_and_is_recorded_as_its_owner(): void
    {
        $this->seed(RoleSeeder::class);
        $supplier = $this->userWithRole('supplier');
        $otherSupplier = $this->userWithRole('supplier');
        $category = Category::factory()->create();

        Sanctum::actingAs($supplier);

        $response = $this->postJson('/api/v1/admin/products', $this->productPayload([
            'category_id' => $category->id,
            'supplier_id' => $otherSupplier->id,
        ]));

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 201)
            ->assertJsonPath('data.name', 'Everyday Abaya');

        $this->assertDatabaseHas('products', [
            'slug' => 'everyday-abaya',
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_admin_can_update_another_suppliers_product(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = $this->userWithRole('admin');
        $supplier = $this->userWithRole('supplier');
        $product = Product::factory()->for($supplier, 'supplier')->create(['name' => 'Original Abaya']);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/v1/admin/products/{$product->id}", [
            'name' => 'Updated Abaya',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Updated Abaya');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Abaya']);
    }

    public function test_supplier_cannot_manage_another_suppliers_product(): void
    {
        $this->seed(RoleSeeder::class);
        $supplier = $this->userWithRole('supplier');
        $otherSupplier = $this->userWithRole('supplier');
        $product = Product::factory()->for($otherSupplier, 'supplier')->create();

        Sanctum::actingAs($supplier);

        $this->patchJson("/api/v1/admin/products/{$product->id}", ['name' => 'Not allowed'])
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->deleteJson("/api/v1/admin/products/{$product->id}")
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_customer_cannot_create_products_and_owner_can_delete_their_own_product(): void
    {
        $this->seed(RoleSeeder::class);
        $customer = $this->userWithRole('customer');
        $supplier = $this->userWithRole('supplier');
        $category = Category::factory()->create();
        $product = Product::factory()->for($supplier, 'supplier')->create();

        Sanctum::actingAs($customer);
        $this->postJson('/api/v1/admin/products', $this->productPayload(['category_id' => $category->id]))
            ->assertForbidden()
            ->assertJsonPath('success', false);

        Sanctum::actingAs($supplier);
        $this->deleteJson("/api/v1/admin/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 200);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('slug', $role)->sole());

        return $user;
    }

    private function productPayload(array $overrides = []): array
    {
        return [
            'name' => 'Everyday Abaya',
            'slug' => 'everyday-abaya',
            'sku' => 'ABAYA-001',
            'price' => 149.99,
            'stock_quantity' => 10,
            'gender' => 'women',
            ...$overrides,
        ];
    }
}
