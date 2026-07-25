<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('points_balance')->default(0);
            $table->decimal('store_credit_balance', 12, 2)->default(0);
            $table->string('vip_tier', 32)->default('member');
            $table->timestamps();
        });
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_wallet_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->integer('points_delta')->default(0);
            $table->decimal('credit_delta', 12, 2)->default(0);
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('idempotency_key', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['loyalty_wallet_id', 'idempotency_key']);
            $table->index(['reference_type', 'reference_id']);
        });
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('code', 32)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referee_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('status', 32)->default('pending');
            $table->string('signup_ip_hash', 64)->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();
            $table->index(['referrer_id', 'status']);
        });
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->decimal('initial_balance', 12, 2);
            $table->decimal('balance', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->foreignId('purchaser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('product_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 16);
            $table->decimal('target_price', 12, 2)->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'product_id', 'product_variant_id', 'type']);
        });
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('email_marketing')->default(false);
            $table->boolean('price_alerts')->default(true);
            $table->boolean('restock_alerts')->default(true);
            $table->boolean('loyalty_updates')->default(true);
            $table->boolean('referral_updates')->default(true);
            $table->timestamps();
        });
        Schema::create('ethical_passports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status', 24)->default('draft');
            $table->string('verification_reference', 128)->unique();
            $table->string('verified_by', 255);
            $table->timestamp('verified_at');
            $table->timestamp('expires_at')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->json('claims');
            $table->timestamps();
            $table->index(['status', 'verified_at']);
        });
        Schema::create('ethical_passport_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethical_passport_id')->constrained()->cascadeOnDelete();
            $table->string('category', 64);
            $table->string('title');
            $table->string('issuer', 255);
            $table->string('reference', 255);
            $table->string('document_url', 2048)->nullable();
            $table->timestamp('verified_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ethical_passport_evidence');
        Schema::dropIfExists('ethical_passports');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('product_alerts');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('referral_codes');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_wallets');
    }
};
