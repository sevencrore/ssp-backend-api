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
        Schema::create('user_bank_transactions', function (Blueprint $table) {
            $table->id();
            // User reference
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Commission IDs (stored as JSON array)
            $table->json('commission_ids_array')->nullable();

            // Bank & customer details
            $table->string('customer_name');
            $table->string('bank_name');
            $table->string('branch');
            $table->string('ifsc_code');
            $table->string('account_number');

            // Amount details
            $table->decimal('amount', 15, 2);
            $table->decimal('admin_charges', 15, 2)->default(0);
            $table->decimal('tsd_charges', 15, 2)->default(0);
            $table->decimal('amount_tobe_paid', 15, 2);

            /**
             * Status:
             * 1 = Success
             * 2 = Failure
             * 3 = Pending
             */
            $table->tinyInteger('status')->default(3); //'1=success, 2=failure, 3=pending

            // Transaction details
            $table->string('utr_number')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_transactions');
    }
};
