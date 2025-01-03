<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNullableFieldsInAddressesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('address', function (Blueprint $table) {
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->integer('city_id')->nullable()->change();
            $table->decimal('latitude', 11, 8)->nullable()->change();
            $table->decimal('longitude', 11, 8)->nullable()->change();
            // user_id remains required, no changes needed for that
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->integer('city_id')->nullable(false)->change();
            $table->decimal('latitude', 11, 8)->default(0)->change();
            $table->decimal('longitude', 11, 8)->default(0)->change();
        });
    }
}
