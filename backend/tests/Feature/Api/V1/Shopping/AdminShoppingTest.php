<?php

namespace Tests\Feature\Api\V1\Shopping;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminShoppingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_manages_catalog_shipping_tax_orders_and_payments(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = $this->userWithRole('admin');
        Sanctum::actingAs($admin);

        $coupon = $this->postJson('/api/v1/admin/coupons', ['code' => 'WELCOME10', 'type' => 'percentage', 'value' => 10, 'is_active' => true])->assertCreated()->json('data.id');
        $this->putJson("/api/v1/admin/coupons/{$coupon}", ['code' => 'WELCOME15', 'type' => 'percentage', 'value' => 15, 'is_active' => true])->assertOk();
        $this->assertDatabaseHas('coupons', ['code' => 'WELCOME15']);

        $method = $this->postJson('/api/v1/admin/shipping-methods', ['name' => 'Express', 'code' => 'express', 'is_active' => true, 'rates' => [['country' => 'GB', 'amount' => 12.5, 'is_active' => true]]])->assertCreated();
        $method->assertJsonPath('data.rates.0.amount', '12.50');
        $this->postJson('/api/v1/admin/tax-rules', ['name' => 'UK VAT', 'country' => 'GB', 'rate' => 20, 'is_active' => true])->assertCreated()->assertJsonPath('data.rate', '20.00');

        $order = $this->order();
        $this->getJson('/api/v1/admin/orders?per_page=1')->assertOk()->assertJsonPath('data.data.0.number', $order->number)->assertJsonPath('data.meta.total', 1);
        $this->postJson("/api/v1/admin/orders/{$order->id}/transition", ['status' => 'processing', 'note' => 'Packed'])->assertOk()->assertJsonPath('data.status', 'processing');
        $this->assertDatabaseHas('order_status_history', ['order_id' => $order->id, 'from_status' => 'pending', 'to_status' => 'processing']);

        $payment = Payment::query()->create(['order_id' => $order->id, 'provider' => 'manual', 'status' => 'pending', 'amount' => 40, 'currency' => 'USD']);
        $this->postJson("/api/v1/admin/payments/{$payment->id}/capture")->assertOk()->assertJsonPath('data.status', 'paid');
        $this->postJson("/api/v1/admin/payments/{$payment->id}/refund", ['amount' => 40])->assertOk()->assertJsonPath('data.status', 'refunded');
        $this->assertDatabaseHas('payment_transactions', ['payment_id' => $payment->id, 'type' => 'refund', 'amount' => 40]);

        $order->update(['status' => 'return_requested']);
        $return = ReturnRequest::query()->create(['order_id' => $order->id, 'user_id' => $order->user_id, 'type' => 'return', 'reason' => 'Does not fit']);
        $this->patchJson("/api/v1/admin/returns/{$return->id}", ['status' => 'received', 'admin_note' => 'Item inspected'])->assertOk()->assertJsonPath('data.status', 'received');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'returned']);
    }

    public function test_supplier_can_only_view_fully_owned_orders_and_customer_is_denied(): void
    {
        $this->seed(RoleSeeder::class);
        $supplier = $this->userWithRole('supplier');
        $otherSupplier = $this->userWithRole('supplier');
        $owned = $this->order($supplier);
        $mixed = $this->order($supplier, $otherSupplier);

        Sanctum::actingAs($supplier);
        $this->getJson('/api/v1/admin/orders')->assertOk()->assertJsonPath('data.data.0.id', $owned->id);
        $this->getJson("/api/v1/admin/orders/{$mixed->id}")->assertNotFound();

        Sanctum::actingAs($this->userWithRole('customer'));
        $this->getJson('/api/v1/admin/orders')->assertForbidden();
        $this->postJson('/api/v1/admin/coupons', ['code' => 'NOPE', 'type' => 'fixed', 'value' => 1, 'is_active' => true])->assertForbidden();
    }

    private function order(?User $supplier = null, ?User $secondSupplier = null): Order
    {
        $customer = User::factory()->create();
        $order = Order::query()->create(['number' => 'VEL-TEST-'.uniqid(), 'user_id' => $customer->id, 'status' => 'pending', 'payment_status' => 'pending', 'currency' => 'USD', 'subtotal' => 40, 'grand_total' => 40]);
        foreach (array_filter([$supplier, $secondSupplier]) as $owner) {
            $product = Product::factory()->for($owner, 'supplier')->create();
            OrderItem::query()->create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name, 'sku' => $product->sku, 'quantity' => 1, 'unit_price' => 20, 'line_total' => 20]);
        }
        if (! $supplier) {
            OrderItem::query()->create(['order_id' => $order->id, 'product_name' => 'Test item', 'sku' => 'TEST', 'quantity' => 1, 'unit_price' => 40, 'line_total' => 40]);
        }

        return $order;
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('slug', $role)->sole());

        return $user;
    }
}
