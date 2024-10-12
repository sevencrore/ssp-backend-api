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
        Schema::table('user_referrals', function (Blueprint $table) {
            //
            $table->id();
            $table->unsignedBigInteger('reg_user_id'); // Add reg_user_id as an unsigned big integer
            $table->unsignedBigInteger('referral_id'); // Add referral_id as an unsigned big integer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_referrals', function (Blueprint $table) {
            //
        });
    }
};
