<?php

namespace Tests\Feature\Api;

use App\Models\PerangkatDaerah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
  use RefreshDatabase;

  public function test_login_mengembalikan_token(): void
  {
    User::factory()->admin()->create([
      'email' => 'admin@sim-kdo.test',
    ]);

    $response = $this->postJson('/api/login', [
      'email' => 'admin@sim-kdo.test',
      'password' => 'password',
      'device_name' => 'postman',
    ]);

    $response->assertOk()
      ->assertJsonPath('data.token_type', 'Bearer')
      ->assertJsonPath('data.user.email', 'admin@sim-kdo.test')
      ->assertJsonStructure([
        'message',
        'data' => ['token', 'token_type', 'user' => ['id', 'role']],
      ]);

    $this->assertDatabaseCount('personal_access_tokens', 1);
  }

  public function test_login_gagal_dengan_password_salah(): void
  {
    User::factory()->create(['email' => 'salah@sim-kdo.test']);

    $this->postJson('/api/login', [
      'email' => 'salah@sim-kdo.test',
      'password' => 'bukan-password',
    ])->assertStatus(401)
      ->assertJsonPath('message', 'Email atau password salah.');
  }

  public function test_login_gagal_karena_validasi(): void
  {
    $this->postJson('/api/login', [])
      ->assertStatus(422)
      ->assertJsonValidationErrors(['email', 'password']);
  }

  public function test_endpoint_terlindungi_menolak_tanpa_token(): void
  {
    $this->getJson('/api/kendaraan')
      ->assertStatus(401)
      ->assertJsonPath('message', 'Unauthenticated.');
  }

  public function test_me_mengembalikan_user_dengan_bearer_token(): void
  {
    $pd = PerangkatDaerah::create([
      'kode_pd' => '1.01.01',
      'nama_pd' => 'Dinas Pendidikan',
      'status' => 'Aktif',
    ]);

    $user = User::factory()->create([
      'email' => 'operator@sim-kdo.test',
      'perangkat_daerah_id' => $pd->id,
    ]);

    $token = $user->createToken('postman')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
      ->getJson('/api/me')
      ->assertOk()
      ->assertJsonPath('data.email', 'operator@sim-kdo.test')
      ->assertJsonPath('data.perangkat_daerah.id', $pd->id);
  }

  public function test_logout_menghapus_token(): void
  {
    $user = User::factory()->admin()->create();

    $token = $user->createToken('postman')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
      ->postJson('/api/logout')
      ->assertOk()
      ->assertJsonPath('message', 'Logout berhasil.');

    $this->assertDatabaseCount('personal_access_tokens', 0);

    /*
     * Pada proses nyata setiap request HTTP membuat instance guard baru,
     * sedangkan di dalam satu proses test instance guard di-cache oleh
     * RequestGuard. Reset guard agar pengecekan token yang sudah dihapus valid.
     */
    $this->app['auth']->forgetGuards();

    $this->withHeader('Authorization', 'Bearer ' . $token)
      ->getJson('/api/me')
      ->assertStatus(401);
  }
}
