<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
  /**
   * Endpoint login tidak membutuhkan autentikasi.
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
      'email' => ['required', 'email'],
      'password' => ['required', 'string'],
      'device_name' => ['nullable', 'string', 'max:100'],
    ];
  }

  /**
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'email.required' => 'Email wajib diisi.',
      'email.email' => 'Format email tidak valid.',
      'password.required' => 'Password wajib diisi.',
    ];
  }
}
