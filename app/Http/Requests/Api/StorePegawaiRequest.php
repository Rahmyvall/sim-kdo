<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePegawaiRequest extends FormRequest
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
      'nama' => ['required', 'string', 'max:100'],
      'nip' => ['required', 'string', 'max:30', 'unique:pegawai,nip'],
      'jabatan' => ['nullable', 'string', 'max:100'],
      'golongan' => ['nullable', 'string', 'max:20'],
      'pangkat' => ['nullable', 'string', 'max:50'],
      'no_telepon' => ['nullable', 'string', 'max:20'],
      'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
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
      'nama.required' => 'Nama pegawai wajib diisi.',
      'nip.required' => 'NIP wajib diisi.',
      'nip.unique' => 'NIP sudah terdaftar.',
      'status.required' => 'Status wajib diisi.',
      'status.in' => 'Status harus bernilai Aktif atau Tidak Aktif.',
    ];
  }
}
