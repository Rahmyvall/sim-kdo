<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'perangkat_daerah_id',
        'nama',
        'nip',
        'jabatan',
        'golongan',
        'pangkat',
        'no_telepon',
        'status'
    ];

    public function perangkatDaerah()
    {
        return $this->belongsTo(PerangkatDaerah::class);
    }

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class);
    }
}
