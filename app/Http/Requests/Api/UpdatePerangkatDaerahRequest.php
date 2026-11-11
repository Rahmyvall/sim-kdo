<?php

namespace App\Http\Requests\Api;

use App\Models\PerangkatDaerah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerangkatDaerahRequest extends FormRequest
{
  /**
   * Hanya admin yang boleh mengelola master data.
   */
  public function authorize(): bool
  {
    return $this->user()?->isAdmin() ?? false;
  }

  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    /** @var PerangkatDaerah|null $perangkatDaerah */
    $perangkatDaerah = $this->route('perangkat_daerah');

    return [
      'kode_pd' => [
        'required',
        'string',
        'max:30',
        Rule::unique('perangkat_daerah', 'kode_pd')->ignore($perangkatDaerah?->id),
      ],
      'nama_pd' => ['required', 'string', 'max:150'],
      'alamat' => ['nullable', 'string', 'max:255'],
      'nama_kepala_pd' => ['nullable', 'string', 'max:100'],
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
      'kode_pd.required' => 'Kode perangkat daerah wajib diisi.',
      'kode_pd.unique' => 'Kode perangkat daerah sudah terdaftar.',
      'nama_pd.required' => 'Nama perangkat daerah wajib diisi.',
      'status.required' => 'Status wajib diisi.',
      'status.in' => 'Status harus bernilai Aktif atau Tidak Aktif.',
    ];
  }
}
