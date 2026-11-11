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
        Schema::create('perangkat_daerah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pd')->unique();
            $table->string('nama_pd');
            $table->text('alamat')->nullable();
            $table->string('nama_kepala_pd')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('status')->default('Aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perangkat_daerah');
    }
};
