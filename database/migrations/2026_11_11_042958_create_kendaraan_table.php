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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('perangkat_daerah_id')
                ->constrained('perangkat_daerah')
                ->restrictOnDelete();

            $table->foreignId('pegawai_id')
                ->nullable()
                ->constrained('pegawai')
                ->nullOnDelete();

            $table->string('nomor_polisi')->unique();
            $table->string('merk');
            $table->string('tipe_model')->nullable();
            $table->year('tahun_produksi')->nullable();
            $table->string('warna')->nullable();
            $table->string('nomor_rangka')->nullable();
            $table->string('nomor_mesin')->nullable();
            $table->string('jenis_kendaraan')->nullable();
            $table->string('bahan_bakar')->nullable();

            $table->enum('kondisi', [
                'Baik',
                'Rusak Ringan',
                'Rusak Berat'
            ])->default('Baik');

            $table->string('status_penggunaan')
                ->default('Digunakan');

            $table->string('nomor_register')->nullable();
            $table->string('kode_barang')->nullable();
            $table->date('tanggal_perolehan')->nullable();
            $table->decimal('nilai_perolehan', 18, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
