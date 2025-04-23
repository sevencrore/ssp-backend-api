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
        Schema::create('vendor_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->float('amount');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('vendor_id')
                  ->references('id')->on('vendors')
                  ->onDelete('set null');

            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_commissions');
    }
};
