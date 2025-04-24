<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rename supplied_by to vendor_id
            $table->renameColumn('supplied_by', 'vendor_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            // Modify vendor_id and set foreign key
            $table->unsignedBigInteger('vendor_id')->nullable()->change();

            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->onDelete('set null');

            // Drop unwanted columns
            $table->dropColumn(['vendor_comission_percentage', 'vendor_comission_total']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['vendor_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            // Rename vendor_id back to supplied_by
            $table->renameColumn('vendor_id', 'supplied_by');

            // Change back to integer
            $table->integer('supplied_by')->change();

            // Re-add dropped columns
            $table->float('vendor_comission_percentage', 8, 2)->after('supplied_by');
            $table->double('vendor_comission_total', 15, 2)->after('vendor_comission_percentage');
        });
    }
};
