<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique('user_id');
        });
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wishlist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['wishlist_id', 'product_id', 'product_variant_id']);
        });
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 3)->default('USD');
            $table->timestamps();
            $table->unique('user_id');
        });
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->timestamps();
            $table->unique(['shopping_cart_id', 'product_id', 'product_variant_id']);
        });
        foreach (['shipping_addresses', 'billing_addresses'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('phone', 40);
                $table->string('line1');
                $table->string('line2')->nullable();
                $table->string('city');
                $table->string('state')->nullable();
                $table->string('postal_code', 32);
                $table->string('country', 2);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')->constrained()->cascadeOnDelete();
            $table->string('country', 2)->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('free_shipping_threshold', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type', 16);
            $table->decimal('value', 12, 2);
            $table->decimal('minimum_order_amount', 12, 2)->nullable();
            $table->decimal('maximum_discount_amount', 12, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('shipping_address_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('billing_address_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipping_method_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending')->index();
            $table->string('payment_status')->default('pending')->index();
            $table->string('currency', 3)->default('USD');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('sku');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount_amount', 12, 2);
            $table->timestamps();
            $table->unique('order_id');
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('status')->default('pending');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->string('provider_reference')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('status');
            $table->decimal('amount', 12, 2);
            $table->string('provider_reference')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['order_status_history', 'payment_transactions', 'payments', 'coupon_usages', 'order_items', 'orders', 'coupons', 'shipping_rates', 'shipping_methods', 'billing_addresses', 'shipping_addresses', 'cart_items', 'shopping_carts', 'wishlist_items', 'wishlists'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
