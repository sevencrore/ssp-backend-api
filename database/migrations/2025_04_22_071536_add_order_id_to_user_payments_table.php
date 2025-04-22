<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToUserPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('user_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('user_id');

            // Add foreign key constraint
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('user_payments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
}
