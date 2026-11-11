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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operator');
            $table->foreignId('perangkat_daerah_id')
                ->nullable()
                ->constrained('perangkat_daerah')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['perangkat_daerah_id']);
            $table->dropColumn(['role', 'perangkat_daerah_id']);
        });
    }
};
