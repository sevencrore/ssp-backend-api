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
        Schema::table('products', function (Blueprint $table) {
            // Add 'category_id' column with the correct type
            $table->unsignedBigInteger('category_id')->after('id'); // Adjust 'after' as needed

            // Define the foreign key constraint
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key constraint and column
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
