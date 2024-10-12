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
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'referral_id', 'sale_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id'); // Add back if necessary
            $table->unsignedBigInteger('referral_id')->nullable(); // Add back if necessary
            $table->unsignedBigInteger('sale_id')->nullable(); // Add back if necessary
        });
    }
};
