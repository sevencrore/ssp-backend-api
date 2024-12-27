<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('business_name');
            $table->bigInteger('phone_1');
            $table->Integer('phone_2')->nullable();
            $table->string('aadhar_number', 12);
            $table->string('address', 255);
            $table->bigInteger('pincode');
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
