<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slideimages', function (Blueprint $table) {
            $table->string('navigate_url')->after('image_path'); // required by default
            $table->string('image_text')->nullable()->after('navigate_url');
        });
    }

    public function down(): void
    {
        Schema::table('slideimages', function (Blueprint $table) {
            $table->dropColumn(['navigate_url', 'image_text']);
        });
    }
};
