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
            $table->string('unit')->nullable()->after('discount'); // Add 'unit' column after 'discount'
            $table->bigInteger('unit_quantity')->nullable()->after('unit'); // Add 'unit_quantity' column after 'unit'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['unit_quantity', 'unit']); // Remove columns in reverse order
        });
    }
};
