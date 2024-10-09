<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::table('products', function (Blueprint $table) {
    //         $table->string('title')->after('id');  // Add 'title'
    //         $table->text('description')->after('title');  // Add 'description'
    //         $table->string('image_url')->after('description');  // Add 'image_url'
    //         $table->decimal('price', 8, 2)->after('image_url');  // Add 'price'
    //     });
    // }
    
    // public function down(): void
    // {
    //     Schema::table('products', function (Blueprint $table) {
    //         $table->dropColumn(['title', 'description', 'image_url', 'price']);  // Drop all added columns
    //     });
    // }
    
};
