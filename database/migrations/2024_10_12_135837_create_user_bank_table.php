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
        Schema::create('user_bank', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name'); // Add bank_name field
            $table->string('account_number')->unique(); // Add account_number field
            $table->string('ifsc_code'); // Add ifsc_code field
            $table->string('branch_name'); // Add branch_name field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank');
    }
};
