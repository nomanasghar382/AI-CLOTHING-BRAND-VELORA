<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('matched_product_id')->nullable()->after('supplier_id')->constrained('products')->nullOnDelete();
            $table->string('catalog_line', 32)->nullable()->after('gender')->index();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('matched_product_id');
            $table->dropColumn('catalog_line');
        });
    }
};
