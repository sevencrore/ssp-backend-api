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
        Schema::create('category', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('title'); // Add 'title'
            $table->text('description')->nullable(); // Add 'description' (nullable)
            $table->string('image_url')->nullable(); // Add 'image_url' (nullable)
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('category'); // Drop the 'category' table
    }
};
