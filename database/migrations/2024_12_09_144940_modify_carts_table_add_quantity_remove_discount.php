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
            // Drop the 'discount' column
            $table->dropColumn('discount');

            // Add the 'quantity' column with default value 0
            $table->integer('quantity')->default(0)->after('product_variants_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Add back the 'discount' column if rollback
            $table->integer('discount')->default(0)->after('product_variants_id');

            // Drop the 'quantity' column
            $table->dropColumn('quantity');
        });
    }
};
