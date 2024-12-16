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
        Schema::create('comission_details', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('comission_id'); 
            $table->integer('level'); 
            $table->float('commission');
            $table->timestamps();

            $table->foreign('comission_id')
                ->references('id')
                ->on('comission') 
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comission_details');
    }
};
