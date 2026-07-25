<?php

namespace Tests\Feature\Api\V1\Shopping;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShoppingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_customer_can_add_update_and_remove_cart_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'price' => 50, 'stock_quantity' => 5]);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 2])
            ->assertCreated()->assertJsonPath('data.items.0.quantity', 2)->assertJsonPath('data.subtotal', '100.00');
        $item = CartItem::query()->sole();
        $this->patchJson("/api/v1/cart/items/{$item->id}", ['quantity' => 3])
            ->assertOk()->assertJsonPath('data.items.0.quantity', 3)->assertJsonPath('data.subtotal', '150.00');
        $this->deleteJson("/api/v1/cart/items/{$item->id}")
            ->assertOk()->assertJsonCount(0, 'data.items');
    }

    public function test_wishlist_add_is_idempotent_and_items_can_be_removed(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/wishlist/items', ['product_id' => $product->id])->assertCreated();
        $this->postJson('/api/v1/wishlist/items', ['product_id' => $product->id])->assertCreated()->assertJsonCount(1, 'data.items');
        $itemId = $this->getJson('/api/v1/wishlist')->json('data.items.0.id');
        $this->deleteJson("/api/v1/wishlist/items/{$itemId}")->assertOk()->assertJsonCount(0, 'data.items');
    }

    public function test_checkout_reprices_cart_reserves_stock_and_creates_order_history(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'price' => 100, 'sale_price' => null, 'stock_quantity' => 5]);
        Coupon::query()->create(['code' => 'TENOFF', 'type' => 'percentage', 'value' => 10, 'is_active' => true]);
        Sanctum::actingAs($user);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 2])->assertCreated();
        $product->update(['price' => 120]);

        $this->postJson('/api/v1/checkout', [
            'coupon_code' => 'TENOFF', 'payment_provider' => 'cod',
            'shipping_address' => ['first_name' => 'Ada', 'last_name' => 'Lovelace', 'phone' => '123456789', 'line1' => '1 Main St', 'city' => 'London', 'postal_code' => 'EC1A1BB', 'country' => 'GB'],
        ])->assertCreated()
            ->assertJsonPath('data.subtotal', '240.00')
            ->assertJsonPath('data.discount_total', '24.00')
            ->assertJsonPath('data.grand_total', '216.00');

        $order = Order::query()->with('items')->sole();
        $this->assertSame(2, $order->items->sole()->quantity);
        $this->assertDatabaseHas('inventories', ['product_id' => $product->id, 'current_stock' => 3, 'reserved_stock' => 2]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 3]);
        $this->assertDatabaseHas('order_status_history', ['order_id' => $order->id, 'to_status' => 'pending']);
        $this->assertDatabaseCount('cart_items', 0);
        $this->getJson('/api/v1/orders')->assertOk()->assertJsonPath('data.0.number', $order->number);
        $this->getJson("/api/v1/orders/{$order->id}")->assertOk()->assertJsonPath('data.shipping_address.country', 'GB');
    }

    public function test_checkout_rejects_stock_that_changed_after_the_cart_was_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'price' => 50, 'stock_quantity' => 3]);
        Sanctum::actingAs($user);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 2])->assertCreated();
        $product->update(['stock_quantity' => 1]);

        $this->postJson('/api/v1/checkout', [
            'payment_provider' => 'cod',
            'shipping_address' => ['first_name' => 'Ada', 'last_name' => 'Lovelace', 'phone' => '123456789', 'line1' => '1 Main St', 'city' => 'London', 'postal_code' => 'EC1A1BB', 'country' => 'GB'],
        ])->assertUnprocessable()->assertJsonPath('success', false);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_stripe_payment_intent_returns_a_safe_envelope_when_not_configured(): void
    {
        config(['services.stripe.secret' => '']);
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'price' => 50, 'stock_quantity' => 3]);
        Sanctum::actingAs($user);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 1])->assertCreated();
        $this->postJson('/api/v1/checkout', [
            'payment_provider' => 'stripe',
            'shipping_address' => ['first_name' => 'Ada', 'last_name' => 'Lovelace', 'phone' => '123456789', 'line1' => '1 Main St', 'city' => 'London', 'postal_code' => 'EC1A1BB', 'country' => 'GB'],
        ])->assertCreated();

        $this->postJson('/api/v1/orders/'.Order::query()->sole()->id.'/payment-intent')
            ->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Stripe payments are not configured.');
    }

    public function test_customer_can_download_html_and_pdf_invoices(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'price' => 50, 'stock_quantity' => 3]);
        Sanctum::actingAs($user);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 1])->assertCreated();
        $this->postJson('/api/v1/checkout', [
            'payment_provider' => 'cod',
            'shipping_address' => ['first_name' => 'Ada', 'last_name' => 'Lovelace', 'phone' => '123456789', 'line1' => '1 Main St', 'city' => 'London', 'postal_code' => 'EC1A1BB', 'country' => 'GB'],
        ])->assertCreated();
        $order = Order::query()->sole();

        $this->get("/api/v1/orders/{$order->id}/invoice/html")
            ->assertOk()->assertHeader('content-type', 'text/html; charset=UTF-8')
            ->assertSeeText("Invoice {$order->number}");
        $this->get("/api/v1/orders/{$order->id}/invoice/pdf")
            ->assertOk()->assertHeader('content-type', 'application/pdf')
            ->assertSee('%PDF');
    }

    public function test_customer_can_request_a_return_and_admin_can_review_it(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'price' => 50, 'stock_quantity' => 3]);
        Sanctum::actingAs($customer);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 1])->assertCreated();
        $this->postJson('/api/v1/checkout', [
            'payment_provider' => 'cod',
            'shipping_address' => ['first_name' => 'Ada', 'last_name' => 'Lovelace', 'phone' => '123456789', 'line1' => '1 Main St', 'city' => 'London', 'postal_code' => 'EC1A1BB', 'country' => 'GB'],
        ])->assertCreated();
        $order = Order::query()->sole();
        $order->update(['status' => 'delivered']);

        $this->postJson("/api/v1/orders/{$order->id}/returns", ['type' => 'return', 'reason' => 'Does not fit'])
            ->assertCreated()
            ->assertJsonPath('success', true);
        $return = ReturnRequest::query()->sole();
        $this->assertDatabaseHas('return_requests', ['id' => $return->id, 'type' => 'return', 'status' => 'requested']);

        $admin = User::factory()->create();
        $role = Role::query()->create(['name' => 'Admin', 'slug' => 'admin']);
        $admin->roles()->attach($role);
        Sanctum::actingAs($admin);
        $this->patchJson("/api/v1/admin/returns/{$return->id}", ['status' => 'approved', 'admin_note' => 'Approved'])
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');
    }
}
