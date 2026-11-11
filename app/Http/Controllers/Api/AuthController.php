<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends ApiController
{
  /**
   * Login untuk mendapatkan Bearer token.
   *
   * POST /api/login
   */
  public function login(LoginRequest $request): JsonResponse
  {
    $user = User::where('email', $request->string('email')->toString())->first();

    if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
      return response()->json([
        'message' => 'Email atau password salah.',
      ], 401);
    }

    $deviceName = $request->string('device_name')->toString() ?: 'postman';

    $token = $user->createToken($deviceName);

    return response()->json([
      'message' => 'Login berhasil.',
      'data' => [
        'token' => $token->plainTextToken,
        'token_type' => 'Bearer',
        'user' => $this->userPayload($user),
      ],
    ]);
  }

  /**
   * Profil user yang sedang login.
   *
   * GET /api/me
   */
  public function me(Request $request): JsonResponse
  {
    $user = $this->currentUser();
    $user->load('perangkatDaerah');

    return response()->json([
      'data' => $this->userPayload($user, true),
    ]);
  }

  /**
   * Logout dan hapus token yang sedang dipakai.
   *
   * POST /api/logout
   */
  public function logout(Request $request): JsonResponse
  {
    $token = $this->currentUser()->currentAccessToken();

    if ($token instanceof PersonalAccessToken) {
      $token->delete();
    } else {
      Auth::guard('web')->logout();
    }

    return response()->json([
      'message' => 'Logout berhasil.',
    ]);
  }

  /**
   * Bentuk data user untuk respons API.
   *
   * @return array<string, mixed>
   */
  private function userPayload(User $user, bool $withPerangkatDaerah = false): array
  {
    $payload = [
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'role' => $user->role,
      'perangkat_daerah_id' => $user->perangkat_daerah_id,
    ];

    if ($withPerangkatDaerah) {
      $payload['perangkat_daerah'] = $user->perangkatDaerah;
    }

    return $payload;
  }
}
