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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('supplied_by')->after('grand_total');
            $table->float('vendor_comission_percentage', 8, 2)->after('supplied_by');
            $table->double('vendor_comission_total', 15, 2)->after('vendor_comission_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('supplied_by');
            $table->dropColumn('vendor_comission_percentage');
            $table->dropColumn('vendor_comission_total');
        });
    }
};
