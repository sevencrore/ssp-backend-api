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
        Schema::table('vendor_commissions', function (Blueprint $table) {
            $table->float('admin_charges_amount')->nullable()->after('amount');
            $table->float('tds_charges_amount')->nullable()->after('admin_charges_amount');
            $table->float('expected_commission')->nullable()->after('tds_charges_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_commissions', function (Blueprint $table) {
            $table->dropColumn(['admin_charges_amount', 'tds_charges_amount', 'expected_commission']);
        });
    }
};
