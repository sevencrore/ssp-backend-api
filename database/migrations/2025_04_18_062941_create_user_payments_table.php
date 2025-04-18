<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('user_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('email'); // Not nullable
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes(); // Soft delete support

            // Foreign key constraint (optional)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_payments');
    }
}
