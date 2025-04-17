<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key for users
            $table->string('order_id')->unique(); // Razorpay Order ID
            $table->decimal('amount', 10, 2); // Amount in INR
            $table->string('currency', 10)->default('INR');
            $table->string('status')->default('created'); // Order status (created, paid, failed)
            $table->text('notes')->nullable(); // Stores extra details (JSON)
            $table->timestamps();
            $table->softDeletes(); // Add soft delete column

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_orders');
    }
};
