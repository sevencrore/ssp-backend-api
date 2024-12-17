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
            

            // Add 'commission_id' as a foreign key referencing 'id' on 'commissions' table
            $table->unsignedBigInteger('comission_id')->after('user_id');
            $table->foreign('comission_id')->references('id')->on('comission')->onDelete('set null');
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
