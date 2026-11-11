<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use SoftDeletes;

    protected $table = 'kendaraan';

    protected $fillable = [
        'perangkat_daerah_id',
        'pegawai_id',
        'nomor_polisi',
        'merk',
        'tipe_model',
        'tahun_produksi',
        'warna',
        'nomor_rangka',
        'nomor_mesin',
        'jenis_kendaraan',
        'bahan_bakar',
        'kondisi',
        'status_penggunaan',
        'nomor_register',
        'kode_barang',
        'tanggal_perolehan',
        'nilai_perolehan',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'nilai_perolehan' => 'decimal:2',
        'tahun_produksi' => 'integer',
    ];

    public function perangkatDaerah()
    {
        return $this->belongsTo(
            PerangkatDaerah::class,
            'perangkat_daerah_id'
        );
    }

    public function pegawai()
    {
        return $this->belongsTo(
            Pegawai::class,
            'pegawai_id'
        );
    }
}
