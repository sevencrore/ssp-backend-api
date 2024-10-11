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
        if (!Schema::hasTable('address')) {
            Schema::create('address', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->integer('city_id');
                $table->text('address');
                $table->string('pin_code');
                $table->string('phone_number');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->decimal('latitude', 11, 8)->default(0);
                $table->decimal('longitude', 11, 8)->default(0);
                $table->timestamps();
            });
        }
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address'); // Drop address table
    }
}
