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
        Schema::table('config_setting', function (Blueprint $table) {
            $table->float('admin_comission_percentage')
                  ->default(8.0)
                  ->after('slideImage_displayCount');
                  
            $table->float('tds_charges_percentage')
                  ->default(2.2)
                  ->after('admin_comission_percentage');

            $table->dropColumn('vendor_comission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_setting', function (Blueprint $table) {
            $table->dropColumn('admin_comission_percentage');
            $table->dropColumn('tds_charges_percentage');
            $table->float('vendor_comission')->nullable(); // restore original column
        });
    }
};
