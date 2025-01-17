<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMiddleNameAndReorderPhone2InVendorsTable extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('middle_name', 255)->nullable()->after('first_name');
            $table->dropColumn('phone_2');
            $table->bigInteger('phone_2')->nullable()->after('phone_1');
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('middle_name');
            $table->dropColumn('phone_2');
            $table->bigInteger('phone_2')->nullable();
        });
    }
}
