<?php

namespace Tests\Feature\Api;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPerangkatDaerahTest extends TestCase
{
  use RefreshDatabase;

  /**
   * @return array<string, string>
   */
  private function tokenFor(User $user): array
  {
    return ['Authorization' => 'Bearer ' . $user->createToken('postman')->plainTextToken];
  }

  private function perangkatDaerah(string $kode = '1.01.01'): PerangkatDaerah
  {
    return PerangkatDaerah::create([
      'kode_pd' => $kode,
      'nama_pd' => 'Dinas ' . $kode,
      'status' => 'Aktif',
    ]);
  }

  /**
   * @return array<string, mixed>
   */
  private function payload(array $override = []): array
  {
    return array_merge([
      'kode_pd' => '1.01.09',
      'nama_pd' => 'Dinas Perhubungan',
      'alamat' => 'Jl. Merdeka No. 1',
      'nama_kepala_pd' => 'Kepala Dinas Perhubungan',
      'no_telepon' => '021-7654321',
      'status' => 'Aktif',
    ], $override);
  }

  public function test_admin_dapat_melihat_daftar_perangkat_daerah(): void
  {
    $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/perangkat-daerah')
      ->assertOk()
      ->assertJsonPath('data.0.kode_pd', '1.01.01')
      ->assertJsonPath('data.0.jumlah_pegawai', 0)
      ->assertJsonPath('data.0.jumlah_kendaraan', 0)
      ->assertJsonStructure(['data', 'links', 'meta']);
  }

  public function test_operator_tidak_dapat_mengakses_perangkat_daerah(): void
  {
    $pd = $this->perangkatDaerah();

    $operator = User::factory()->create([
      'perangkat_daerah_id' => $pd->id,
    ]);

    $this->withHeaders($this->tokenFor($operator))
      ->getJson('/api/perangkat-daerah')
      ->assertStatus(403);
  }

  public function test_admin_dapat_menambah_perangkat_daerah(): void
  {
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->postJson('/api/perangkat-daerah', $this->payload())
      ->assertStatus(201)
      ->assertJsonPath('message', 'Perangkat daerah berhasil ditambahkan.')
      ->assertJsonPath('data.kode_pd', '1.01.09');

    $this->assertDatabaseHas('perangkat_daerah', [
      'kode_pd' => '1.01.09',
      'nama_pd' => 'Dinas Perhubungan',
    ]);
  }

  public function test_store_perangkat_daerah_gagal_karena_validasi(): void
  {
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->postJson('/api/perangkat-daerah', [])
      ->assertStatus(422)
      ->assertJsonValidationErrors(['kode_pd', 'nama_pd', 'status']);
  }

  public function test_admin_dapat_menampilkan_detail_beserta_relasi(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    Pegawai::create([
      'perangkat_daerah_id' => $pd->id,
      'nama' => 'Budi',
      'nip' => '198701012010011001',
      'status' => 'Aktif',
    ]);

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/perangkat-daerah/' . $pd->id)
      ->assertOk()
      ->assertJsonPath('data.id', $pd->id)
      ->assertJsonCount(1, 'data.pegawai')
      ->assertJsonCount(0, 'data.kendaraan');
  }

  public function test_admin_dapat_memperbarui_perangkat_daerah(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->putJson('/api/perangkat-daerah/' . $pd->id, $this->payload([
        'kode_pd' => $pd->kode_pd,
        'nama_pd' => 'Dinas Pendidikan dan Kebudayaan',
      ]))
      ->assertOk()
      ->assertJsonPath('data.nama_pd', 'Dinas Pendidikan dan Kebudayaan');
  }

  public function test_admin_dapat_menghapus_perangkat_daerah_kosong(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->deleteJson('/api/perangkat-daerah/' . $pd->id)
      ->assertOk()
      ->assertJsonPath('message', 'Perangkat daerah berhasil dihapus.');

    $this->assertSoftDeleted('perangkat_daerah', ['id' => $pd->id]);
  }

  public function test_hapus_perangkat_daerah_berisi_relasi_ditolak(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    Kendaraan::create([
      'perangkat_daerah_id' => $pd->id,
      'nomor_polisi' => 'B 1234 ABC',
      'merk' => 'Toyota',
      'jenis_kendaraan' => 'Mobil',
      'kondisi' => 'Baik',
      'status_penggunaan' => 'Digunakan',
    ]);

    $this->withHeaders($this->tokenFor($admin))
      ->deleteJson('/api/perangkat-daerah/' . $pd->id)
      ->assertStatus(409)
      ->assertJsonPath(
        'message',
        'Perangkat daerah masih memiliki data pegawai/kendaraan.'
      );

    $this->assertDatabaseHas('perangkat_daerah', [
      'id' => $pd->id,
      'deleted_at' => null,
    ]);
  }

  public function test_perangkat_daerah_tidak_ditemukan_404(): void
  {
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/perangkat-daerah/999999')
      ->assertStatus(404);
  }
}
