<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('address', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('first_name'); // First name
            $table->string('last_name'); // Last name
            $table->integer('city_id'); // City ID
            $table->text('address'); // Address
            $table->string('pin_code'); // Pin code
            $table->string('phone_number'); // Phone number
            $table->unsignedBigInteger('user_id'); // User ID
            $table->decimal('latitude', 11, 8)->default(0.00000000); // Latitude
            $table->decimal('longitude', 11, 8)->default(0.00000000); // Longitude
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address'); // Drop address table
    }
}
