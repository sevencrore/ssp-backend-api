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
            $table->integer('max_level')->after('referal_incentive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_setting', function (Blueprint $table) {
            $table->dropColumn('max_level'); // Drop max_level column
        });
    }
};
