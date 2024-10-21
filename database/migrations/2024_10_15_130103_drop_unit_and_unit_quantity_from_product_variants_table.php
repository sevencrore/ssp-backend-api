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
            $table->dropColumn(['unit_quantity', 'unit']); // Drop the columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('discount'); // Re-add 'unit' column
            $table->bigInteger('unit_quantity')->nullable()->after('unit'); // Re-add 'unit_quantity' column
        });
    }
};