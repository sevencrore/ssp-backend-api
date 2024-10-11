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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID

            // Nullable fields for user_id and address_id as integers
            $table->integer('user_id')->nullable(); // User ID
            $table->integer('address_id')->nullable(); // Address ID

            // Additional fields
            $table->integer('order_status')->default(0); // Default order status
            $table->string('tracking_number')->nullable(); // Tracking number

            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
