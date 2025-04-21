<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slideimages', function (Blueprint $table) {
            $table->integer('order_number')
                ->default(1) // Set default value
                ->after('image_text');
        });
    }

    public function down(): void
    {
        Schema::table('slideimages', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};
