<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('style_preferences')->nullable();
            $table->json('color_preferences')->nullable();
            $table->json('avoidances')->nullable();
            $table->json('quiz_answers')->nullable();
            $table->string('default_budget_currency', 3)->default('USD');
            $table->timestamps();
        });
        Schema::create('body_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('fit_preference')->nullable();
            $table->string('height_unit', 8)->nullable();
            $table->decimal('height', 6, 2)->nullable();
            $table->json('measurements')->nullable();
            $table->json('size_preferences')->nullable();
            $table->timestamps();
        });
        Schema::create('style_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
        Schema::create('style_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role', 16);
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('style_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 32)->default('general');
            $table->string('status', 32)->default('completed');
            $table->json('request_context')->nullable();
            $table->json('weather')->nullable();
            $table->json('response')->nullable();
            $table->string('provider', 32)->default('catalog_fallback');
            $table->timestamp('generated_at');
            $table->timestamps();
        });
        Schema::create('style_recommendation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_recommendation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('rank');
            $table->string('reason')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->json('product_snapshot')->nullable();
            $table->timestamps();
            $table->unique(['style_recommendation_id', 'product_id'], 'style_rec_item_rec_prod_uniq');
        });
        Schema::create('ai_response_caches', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key', 64)->unique();
            $table->json('payload');
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
        Schema::create('fashion_trends', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description');
            $table->json('tags')->nullable();
            $table->string('season', 32)->nullable();
            $table->decimal('confidence', 5, 2)->default(0);
            $table->string('source', 64)->default('editorial');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
        Schema::create('saved_outfits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_recommendation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('occasion')->nullable();
            $table->text('notes')->nullable();
            $table->json('items');
            $table->timestamps();
        });
        Schema::create('style_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_recommendation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 32);
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('comment')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('style_feedback');
        Schema::dropIfExists('saved_outfits');
        Schema::dropIfExists('fashion_trends');
        Schema::dropIfExists('ai_response_caches');
        Schema::dropIfExists('style_recommendation_items');
        Schema::dropIfExists('style_recommendations');
        Schema::dropIfExists('style_messages');
        Schema::dropIfExists('style_conversations');
        Schema::dropIfExists('body_profiles');
        Schema::dropIfExists('ai_profiles');
    }
};
