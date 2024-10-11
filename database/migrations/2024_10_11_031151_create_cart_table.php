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
        Schema::create('cart', function (Blueprint $table) {
            $table->increments('id'); // Auto-incrementing ID
            
            $table->unsignedInteger('user_id'); // User ID as unsigned integer
            $table->unsignedInteger('product_id'); // Product ID as unsigned integer
            
            $table->timestamps(); // Includes created_at and updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
