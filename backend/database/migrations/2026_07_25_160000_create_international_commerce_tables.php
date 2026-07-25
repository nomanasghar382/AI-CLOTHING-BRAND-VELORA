<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('name');
            $table->string('default_currency', 3)->nullable();
            $table->string('default_language', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('code', 16);
            $table->string('name');
            $table->timestamps();
            $table->unique(['country_id', 'code']);
        });
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('postal_prefix', 16)->nullable();
            $table->timestamps();
        });
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->string('symbol', 8);
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->string('base_currency', 3)->default('USD');
            $table->decimal('rate', 18, 8);
            $table->string('source')->default('manual');
            $table->timestamp('effective_at');
            $table->timestamps();
            $table->index(['currency_id', 'effective_at']);
        });
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('group');
            $table->string('key');
            $table->text('value');
            $table->timestamps();
            $table->unique(['language_id', 'group', 'key']);
        });
        Schema::create('regional_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('size_code');
            $table->string('region', 8);
            $table->string('mapped_size_code');
            $table->string('category')->nullable();
            $table->timestamps();
            $table->unique(['size_code', 'region', 'category']);
        });
        Schema::create('country_product_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('local_sku')->nullable();
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['country_id', 'product_id']);
        });
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('shipping_zone_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->unique(['shipping_zone_id', 'country_id']);
        });
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
        Schema::create('delivery_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipping_carrier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('minimum_order_amount', 12, 2)->nullable();
            $table->decimal('base_amount', 12, 2)->default(0);
            $table->unsignedSmallInteger('min_days')->default(1);
            $table->unsignedSmallInteger('max_days')->default(7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('duty_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('hs_code_prefix', 16)->nullable();
            $table->decimal('rate', 5, 2);
            $table->decimal('threshold', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('product_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_available')->default(true);
            $table->string('restriction_reason')->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'country_id']);
        });
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('country', 2);
            $table->string('state')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('warehouse_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('available_stock')->default(0);
            $table->unsignedInteger('reserved_stock')->default(0);
            $table->timestamps();
            $table->unique(['warehouse_id', 'product_id', 'product_variant_id'], 'wh_inv_wh_prod_var_uniq');
        });
        Schema::create('international_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 40);
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('postal_code', 32);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('shipping_method_id')->constrained()->nullOnDelete();
            $table->foreignId('shipping_carrier_id')->nullable()->after('warehouse_id')->constrained()->nullOnDelete();
            $table->decimal('duty_total', 12, 2)->default(0)->after('tax_total');
            $table->decimal('exchange_rate', 18, 8)->default(1)->after('currency');
            $table->string('locale', 10)->nullable()->after('currency');
        });
        Schema::create('shipping_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipping_carrier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('country', 2);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->unsignedSmallInteger('min_days');
            $table->unsignedSmallInteger('max_days');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
        Schema::create('shipment_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipping_carrier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tracking_number')->unique();
            $table->string('status')->default('pending');
            $table->string('tracking_url')->nullable();
            $table->json('events')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_trackings');
        Schema::dropIfExists('shipping_estimates');
        Schema::table('orders', fn (Blueprint $table) => $table->dropConstrainedForeignId('shipping_carrier_id')->dropConstrainedForeignId('warehouse_id')->dropColumn(['duty_total', 'exchange_rate', 'locale']));
        foreach (['international_addresses', 'warehouse_inventories', 'warehouses', 'product_availabilities', 'duty_rules', 'delivery_rules', 'shipping_carriers', 'shipping_zone_countries', 'shipping_zones', 'country_product_mappings', 'regional_sizes', 'translations', 'languages', 'currency_rates', 'currencies', 'cities', 'states', 'countries'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
