<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_history', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key from users table

            // 1 => referal_incentive 
            // 2 => self-buy
            // 3 => first_referal_buy
            // 4 => second_referal_buy
            $table->tinyInteger('commission_type')->nullable(); // Tiny integer for commission type
            $table->unsignedBigInteger('referal_id')->nullable(); // Foreign key from users table, nullable
            $table->float('amount'); // Float for the commission amount
            $table->text('description')->nullable(); // Text for description, nullable
            $table->timestamps(); // Timestamps for created_at and updated_at

            // Define foreign key constraints
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade'); // Cascade on delete

            $table->foreign('referal_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Set to null on delete
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commission_history');
    }
}
