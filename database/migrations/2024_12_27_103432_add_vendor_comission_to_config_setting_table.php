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
            $table->float('vendor_comission', 8, 2)->nullable()->after('max_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_setting', function (Blueprint $table) {
            $table->dropColumn('vendor_comission');
        });
    }
};
