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
            $table->float('minimum_basepay_amount', 8, 2)
                  ->default(0)
                  ->after('vendor_comission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_setting', function (Blueprint $table) {
            $table->dropColumn('minimum_basepay_amount');
        });
    }
};