<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 80)->nullable()->after('name');
            $table->string('last_name', 80)->nullable()->after('first_name');
            $table->string('phone', 30)->nullable()->unique()->after('email');
            $table->string('avatar_url')->nullable()->after('phone');
            $table->string('locale', 10)->default('en')->after('avatar_url');
            $table->string('timezone', 64)->default('UTC')->after('locale');
            $table->boolean('is_active')->default(true)->index()->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'avatar_url',
                'locale',
                'timezone',
                'is_active',
            ]);
        });
    }
};
