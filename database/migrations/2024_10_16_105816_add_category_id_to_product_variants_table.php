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
        // Schema::table('product_variants', function (Blueprint $table) {
        //     // $table->Integer('category_id')->after('product_id'); // Match data type
        //    // $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade'); // Explicit foreign key definition
        // });
    }

    // /**
    //  * Reverse the migrations.
    //  */
    public function down(): void
    {
    //    Schema::table('product_variants', function (Blueprint $table) {
    //         $table->dropForeign(['category_id']); // Drop the foreign key constraint
    //         $table->dropColumn('category_id'); // Drop the column
    //     });
    }
};
