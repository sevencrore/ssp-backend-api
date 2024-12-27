<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerVendorTable extends Migration
{
    public function up()
    {
        Schema::create('customer_vendor', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('vendor_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_vendor');
    }
}
