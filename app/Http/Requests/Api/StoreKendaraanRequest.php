<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKendaraanRequest extends FormRequest
{
  /**
   * Autentikasi ditangani middleware auth:sanctum.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'perangkat_daerah_id' => [
        $this->user()?->isAdmin() ? 'required' : 'nullable',
        'exists:perangkat_daerah,id',
      ],
      'pegawai_id' => ['nullable', 'exists:pegawai,id'],
      'nomor_polisi' => ['required', 'string', 'max:20', 'unique:kendaraan,nomor_polisi'],
      'merk' => ['required', 'string', 'max:100'],
      'tipe_model' => ['nullable', 'string', 'max:100'],
      'tahun_produksi' => ['nullable', 'integer', 'between:1900,2100'],
      'warna' => ['nullable', 'string', 'max:40'],
      'nomor_rangka' => ['nullable', 'string', 'max:100'],
      'nomor_mesin' => ['nullable', 'string', 'max:100'],
      'jenis_kendaraan' => ['required', 'string', 'max:60'],
      'bahan_bakar' => ['nullable', 'string', 'max:40'],
      'kondisi' => [
        'required',
        Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat']),
      ],
      'status_penggunaan' => [
        'required',
        Rule::in([
          'Digunakan',
          'Tidak Digunakan',
          'Dalam Perawatan',
          'Dihapuskan',
        ]),
      ],
      'nomor_register' => ['nullable', 'string', 'max:100'],
      'kode_barang' => ['nullable', 'string', 'max:100'],
      'tanggal_perolehan' => ['nullable', 'date'],
      'nilai_perolehan' => ['nullable', 'numeric', 'min:0'],
    ];
  }

  /**
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'perangkat_daerah_id.required' => 'Perangkat daerah wajib dipilih.',
      'perangkat_daerah_id.exists' => 'Perangkat daerah tidak ditemukan.',
      'pegawai_id.exists' => 'Pegawai tidak ditemukan.',
      'nomor_polisi.required' => 'Nomor polisi wajib diisi.',
      'nomor_polisi.unique' => 'Nomor polisi sudah terdaftar.',
      'merk.required' => 'Merk wajib diisi.',
      'jenis_kendaraan.required' => 'Jenis kendaraan wajib diisi.',
      'kondisi.required' => 'Kondisi kendaraan wajib diisi.',
      'kondisi.in' => 'Kondisi harus salah satu dari: Baik, Rusak Ringan, Rusak Berat.',
      'status_penggunaan.required' => 'Status penggunaan wajib diisi.',
      'status_penggunaan.in' => 'Status penggunaan tidak valid.',
    ];
  }
}
