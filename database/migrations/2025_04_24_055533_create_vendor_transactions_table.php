<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendor_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained('vendors')
                ->onDelete('set null');

            $table->foreignId('paid_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->json('vendor_commission_id_array')->nullable();

            $table->float('total_amount');
            $table->float('admin_commission_amount');
            $table->float('tds_charges_amount');
            $table->integer('amount_paid')->default(1);

            $table->string('transaction_id');
            $table->integer('status');
            $table->string('attachment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_transactions');
    }
};
