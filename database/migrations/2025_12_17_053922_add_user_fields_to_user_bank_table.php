<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_bank', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('a_c_holder_name')->after('user_id');
            $table->string('phone_number')->after('a_c_holder_name');
            $table->string('pancard')->nullable()->after('phone_number');
            $table->string('aadharcard')->nullable()->after('pancard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_bank', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'a_c_holder_name',
                'phone_number',
                'pancard',
                'aadharcard',
            ]);
        });
    }
};
