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
        Schema::create('earnings', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key for the user
            $table->unsignedBigInteger('referral_id')->nullable(); // Optional referral_id for tracking
            $table->unsignedBigInteger('sale_id')->nullable(); // Optional sale_id for tracking
            $table->unsignedBigInteger('referral_incentive')->default(0);
            $table->unsignedBigInteger('sale_value_estimated')->default(0);
            $table->unsignedBigInteger('sale_actual_value')->default(0);
            $table->unsignedBigInteger('wallet_amount')->default(0);
            $table->unsignedBigInteger('self_purchase_total')->default(0);
            $table->unsignedBigInteger('first_referral_purchase_total')->default(0);
            $table->unsignedBigInteger('second_referral_purchase_total')->default(0);
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
