<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionIdToUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            
                $table->dropColumn('commission');
                $table->dropColumn('minimum_order');
            

                $table->unsignedBigInteger('comission_id')->nullable()->after('user_id');; // Add the comission_id column
                $table->foreign('comission_id') // Define the foreign key constraint
                      ->references('id')
                      ->on('comission')
                      ->onDelete('set null'); // Set to null if the referenced record is deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Drop the foreign key and 'commission_id' column
                $table->dropForeign(['commission_id']);
                $table->dropColumn('commission_id');

            // Re-add 'commission' and 'minimum_order' columns
            $table->string('commission')->nullable();
            $table->integer('minimum_order')->nullable();
        });
    }
}
