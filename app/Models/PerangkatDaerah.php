<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerangkatDaerah extends Model
{
    use SoftDeletes;

    protected $table = 'perangkat_daerah';

    protected $fillable = [
        'kode_pd',
        'nama_pd',
        'alamat',
        'nama_kepala_pd',
        'no_telepon',
        'status'
    ];

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }
}
