<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::table('product_variants', function (Blueprint $table) {
    //         $table->foreignId('category_id')->after('product_id')->constrained('category')->onDelete('cascade'); // Add the category_id column
    //     });
    // }

    // /**
    //  * Reverse the migrations.
    //  */
    // public function down(): void
    // {
    //     Schema::table('product_variants', function (Blueprint $table) {
    //         $table->dropForeign(['category_id']); // Remove the foreign key
    //         $table->dropColumn('category_id'); // Remove the column if rolled back
    //     });
    // }
};
