<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('query')->index();
            $table->unsignedInteger('results_count')->default(0);
            $table->string('source', 40)->default('catalog');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('search_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('query')->index();
            $table->unsignedBigInteger('search_count')->default(0);
            $table->unsignedBigInteger('click_count')->default(0);
            $table->unsignedBigInteger('conversion_count')->default(0);
            $table->timestamp('last_searched_at')->nullable();
            $table->timestamps();
            $table->unique('query');
        });

        Schema::create('search_synonyms', function (Blueprint $table) {
            $table->id();
            $table->string('term')->index();
            $table->string('synonym');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_synonyms');
        Schema::dropIfExists('search_analytics');
        Schema::dropIfExists('search_histories');
    }
};
