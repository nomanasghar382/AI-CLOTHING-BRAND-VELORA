<?php

namespace Tests\Feature\Api\V1\Shopping;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
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
}
