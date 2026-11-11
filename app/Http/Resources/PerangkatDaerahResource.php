<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PerangkatDaerah
 */
class PerangkatDaerahResource extends JsonResource
{
  /**
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'kode_pd' => $this->kode_pd,
      'nama_pd' => $this->nama_pd,
      'alamat' => $this->alamat,
      'nama_kepala_pd' => $this->nama_kepala_pd,
      'no_telepon' => $this->no_telepon,
      'status' => $this->status,
      'jumlah_pegawai' => $this->whenCounted('pegawai'),
      'jumlah_kendaraan' => $this->whenCounted('kendaraan'),
      'pegawai' => PegawaiResource::collection(
        $this->whenLoaded('pegawai')
      ),
      'kendaraan' => KendaraanResource::collection(
        $this->whenLoaded('kendaraan')
      ),
      'created_at' => $this->created_at?->toIso8601String(),
      'updated_at' => $this->updated_at?->toIso8601String(),
    ];
  }
}
