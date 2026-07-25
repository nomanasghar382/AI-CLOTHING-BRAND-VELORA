<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->string('status')->default('pending')->index();
            $table->string('tax_id')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('supplier_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('warehouse');
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('city');
            $table->string('region')->nullable();
            $table->string('postal_code', 32);
            $table->string('country', 2);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
        Schema::create('supplier_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('name');
            $table->string('url');
            $table->string('status')->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('supplier_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('issuer')->nullable();
            $table->string('reference')->nullable();
            $table->string('status')->default('pending');
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('supplier_profile_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
        Schema::create('supplier_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->index();
            $table->integer('on_hand')->default(0);
            $table->integer('reserved')->default(0);
            $table->integer('available')->default(0);
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->unique(['supplier_profile_id', 'product_id']);
        });
        Schema::create('inventory_sync_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->string('source')->default('api');
            $table->string('status')->default('queued')->index();
            $table->json('payload')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('inventory_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_sync_job_id')->constrained()->cascadeOnDelete();
            $table->string('level');
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
        });
        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_inventory_id')->constrained('supplier_inventory')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->integer('quantity');
            $table->string('status')->default('active')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_inventory_id')->constrained('supplier_inventory')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('quantity_delta');
            $table->string('reason');
            $table->string('idempotency_key')->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('from_location');
            $table->string('to_location');
            $table->integer('quantity');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft')->index();
            $table->decimal('total', 14, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->json('items');
            $table->date('expected_at')->nullable();
            $table->timestamps();
        });
        Schema::create('supplier_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_number')->nullable()->index();
            $table->string('carrier')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
        Schema::create('supplier_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->unique();
            $table->string('status')->default('requested');
            $table->text('reason');
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('supplier_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->string('status')->default('pending');
            $table->decimal('gross_amount', 14, 2);
            $table->decimal('fee_amount', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2);
            $table->string('currency', 3)->default('USD');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_settlement_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('status')->default('pending');
            $table->decimal('amount', 14, 2);
            $table->string('method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
        Schema::create('supplier_performance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->date('period_date');
            $table->decimal('fulfillment_rate', 5, 2)->default(0);
            $table->decimal('on_time_rate', 5, 2)->default(0);
            $table->decimal('defect_rate', 5, 2)->default(0);
            $table->decimal('score', 5, 2)->default(0);
            $table->json('metrics')->nullable();
            $table->timestamps();
            $table->unique(['supplier_profile_id', 'period_date']);
        });
        Schema::create('supplier_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject')->nullable();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_profile_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('secret');
            $table->json('events');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('bi_dashboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('dashboard')->index();
            $table->date('snapshot_date');
            $table->json('metrics');
            $table->timestamps();
            $table->unique(['dashboard', 'snapshot_date']);
        });
        Schema::create('bi_customer_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('metric_date');
            $table->decimal('lifetime_value', 14, 2)->default(0);
            $table->unsignedInteger('order_count')->default(0);
            $table->json('metrics')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'metric_date']);
        });
        Schema::create('bi_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->index();
            $table->string('query')->nullable()->index();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
        Schema::create('bi_forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->date('forecast_date');
            $table->decimal('value', 14, 2);
            $table->json('context')->nullable();
            $table->timestamps();
            $table->unique(['type', 'product_id', 'forecast_date']);
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropConstrainedForeignId('supplier_profile_id'));
        foreach (['bi_forecasts', 'bi_analytics_events', 'bi_customer_metrics', 'bi_dashboard_snapshots', 'webhook_endpoints', 'supplier_messages', 'supplier_performance_snapshots', 'supplier_payments', 'supplier_settlements', 'supplier_returns', 'supplier_shipments', 'purchase_orders', 'inventory_transfers', 'inventory_adjustments', 'inventory_reservations', 'inventory_sync_logs', 'inventory_sync_jobs', 'supplier_inventory', 'supplier_certifications', 'supplier_documents', 'supplier_addresses', 'supplier_profiles'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
