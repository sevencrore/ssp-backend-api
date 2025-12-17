<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comission_history', function (Blueprint $table) {
            $table->integer('is_paid')
                ->default(0)
                ->after('amount');//('0 = unpaid, 1 = paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comission_history', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
};
