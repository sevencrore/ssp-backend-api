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
            $table->tinyInteger('default_vendor_id')->nullable()->after('max_level'); // Add default_vendor_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_setting', function (Blueprint $table) {
            $table->dropColumn('default_vendor_id'); // Drop default_vendor_id column
        });
    }
};
