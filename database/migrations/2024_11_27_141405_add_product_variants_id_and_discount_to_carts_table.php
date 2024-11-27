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
        Schema::table('carts', function (Blueprint $table) {
            $table->Integer('product_variants_id')->after('product_id'); // Add product_variant_id column
            $table->integer('discount')->default(0)->after('product_variants_id'); // Add discount column with default value 0
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['product_variants_id', 'discount']); // Drop the columns
        });
    }
};
