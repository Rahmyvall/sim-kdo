<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Kendaraan
 */
class KendaraanResource extends JsonResource
{
  /**
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'nomor_polisi' => $this->nomor_polisi,
      'merk' => $this->merk,
      'tipe_model' => $this->tipe_model,
      'tahun_produksi' => $this->tahun_produksi,
      'warna' => $this->warna,
      'nomor_rangka' => $this->nomor_rangka,
      'nomor_mesin' => $this->nomor_mesin,
      'jenis_kendaraan' => $this->jenis_kendaraan,
      'bahan_bakar' => $this->bahan_bakar,
      'kondisi' => $this->kondisi,
      'status_penggunaan' => $this->status_penggunaan,
      'nomor_register' => $this->nomor_register,
      'kode_barang' => $this->kode_barang,
      'tanggal_perolehan' => $this->tanggal_perolehan?->format('Y-m-d'),
      'nilai_perolehan' => $this->nilai_perolehan,
      'perangkat_daerah_id' => $this->perangkat_daerah_id,
      'pegawai_id' => $this->pegawai_id,
      'perangkat_daerah' => PerangkatDaerahResource::make(
        $this->whenLoaded('perangkatDaerah')
      ),
      'pegawai' => PegawaiResource::make(
        $this->whenLoaded('pegawai')
      ),
      'created_at' => $this->created_at?->toIso8601String(),
      'updated_at' => $this->updated_at?->toIso8601String(),
    ];
  }
}
