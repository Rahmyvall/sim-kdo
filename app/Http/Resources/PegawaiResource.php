<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Pegawai
 */
class PegawaiResource extends JsonResource
{
  /**
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'nama' => $this->nama,
      'nip' => $this->nip,
      'jabatan' => $this->jabatan,
      'golongan' => $this->golongan,
      'pangkat' => $this->pangkat,
      'no_telepon' => $this->no_telepon,
      'status' => $this->status,
      'perangkat_daerah_id' => $this->perangkat_daerah_id,
      'perangkat_daerah' => PerangkatDaerahResource::make(
        $this->whenLoaded('perangkatDaerah')
      ),
      'created_at' => $this->created_at?->toIso8601String(),
      'updated_at' => $this->updated_at?->toIso8601String(),
    ];
  }
}
