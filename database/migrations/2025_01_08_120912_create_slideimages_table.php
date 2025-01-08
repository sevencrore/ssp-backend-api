<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('slideimages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Optional title for the image
            $table->string('image_path');       // Path to the image file
            $table->timestamps();               // Created at and Updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('slideimages');
    }
};
