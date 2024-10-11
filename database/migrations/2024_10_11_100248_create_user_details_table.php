<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('first_name'); // First name
            $table->string('middle_name')->nullable(); // Middle name (optional)
            $table->string('last_name'); // Last name
            $table->string('phone_1'); // Primary phone number
            $table->string('phone_2')->nullable(); // Secondary phone number (optional)
            $table->string('email'); // Email address
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key for user, made nullable
            $table->timestamps(); // Created and updated timestamps

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'); // Change to cascade or set null as per your requirement
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details'); // Drop user_details table
    }
}
