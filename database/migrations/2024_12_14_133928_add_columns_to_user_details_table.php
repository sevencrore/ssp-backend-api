<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->integer('minimum_order')->after('referral_code'); // Add minimum_order column
            $table->float('commission')->after('minimum_order'); // Add commission column
            $table->tinyInteger('is_first_order_completed')->after('commission')->default(0); // Add is_first_order_completed column with default value 0
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn(['minimum_order', 'commission', 'is_first_order_completed']); // Remove the added columns
        });
    }
}
