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
        Schema::table('product_variants', function (Blueprint $table) {
            // Add the columns after the 'discount' column
            $table->integer('unit_id')->after('discount'); // Adding unit_id
            $table->integer('unit_quantity')->after('unit_id'); // Adding unit_quantity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Drop the columns in reverse order
            $table->dropColumn('unit_quantity');
            $table->dropColumn('unit_id');
        });
    }
};
