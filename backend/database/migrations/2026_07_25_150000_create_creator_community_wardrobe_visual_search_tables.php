<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('handle')->unique();
            $table->string('display_name');
            $table->text('bio')->nullable();
            $table->string('avatar_url')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_accepting_commissions')->default(false);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('creator_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_url')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
        Schema::create('creator_collection_products', function (Blueprint $table) {
            $table->foreignId('creator_collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->primary(['creator_collection_id', 'product_id']);
        });
        Schema::create('creator_follows', function (Blueprint $table) {
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['creator_profile_id', 'user_id']);
        });
        Schema::create('creator_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('pending');
            $table->timestamp('earned_at')->nullable();
            $table->timestamps();
        });
        Schema::create('creator_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('destination', 64);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        Schema::create('lookboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_url')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
        Schema::create('lookboard_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lookboard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_url')->nullable();
            $table->json('position')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('wardrobe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->string('category')->nullable();
            $table->string('color')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('visual_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('image_url');
            $table->string('image_hash', 64)->index();
            $table->string('provider', 64)->default('catalog_fallback');
            $table->json('results');
            $table->timestamps();
        });
        Schema::create('visual_similarity_caches', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key', 64)->unique();
            $table->json('results');
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('body', 2000);
            $table->json('media')->nullable();
            $table->string('status')->default('published')->index();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->timestamps();
        });
        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('community_comments')->cascadeOnDelete();
            $table->string('body', 1000);
            $table->string('status')->default('published');
            $table->timestamps();
        });
        Schema::create('community_likes', function (Blueprint $table) {
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['community_post_id', 'user_id']);
        });
        Schema::create('community_bookmarks', function (Blueprint $table) {
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['community_post_id', 'user_id']);
        });
        Schema::create('community_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->string('status')->default('open');
            $table->timestamps();
        });
        Schema::create('community_moderations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->string('note')->nullable();
            $table->timestamps();
        });
        Schema::create('community_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['community_activities', 'community_moderations', 'community_reports', 'community_bookmarks', 'community_likes', 'community_comments', 'community_posts', 'visual_similarity_caches', 'visual_searches', 'wardrobe_items', 'lookboard_items', 'lookboards', 'creator_withdrawals', 'creator_commissions', 'creator_follows', 'creator_collection_products', 'creator_collections', 'creator_profiles'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
