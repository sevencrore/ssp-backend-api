<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('order_id')->unsigned()->nullable(false); // Order ID
            $table->integer('product_id')->unsigned()->nullable(false); // Product ID
            $table->integer('user_id')->unsigned()->nullable(false); // User ID

            $table->integer('quantity')->unsigned(); // Quantity of product
            $table->integer('price')->unsigned(); // Price in integer (e.g., cents)

            $table->timestamps(); // Includes created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
