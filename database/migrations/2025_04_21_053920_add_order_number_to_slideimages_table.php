<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slideimages', function (Blueprint $table) {
            $table->integer('order_number')->length(10)->nullable()->after('image_text');
        });
    }

    public function down(): void
    {
        Schema::table('slideimages', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};
