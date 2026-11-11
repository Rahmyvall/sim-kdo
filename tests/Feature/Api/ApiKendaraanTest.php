<?php

namespace Tests\Feature\Api;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKendaraanTest extends TestCase
{
  use RefreshDatabase;

  private function perangkatDaerah(string $kode = '1.01.01'): PerangkatDaerah
  {
    return PerangkatDaerah::create([
      'kode_pd' => $kode,
      'nama_pd' => 'Dinas ' . $kode,
      'status' => 'Aktif',
    ]);
  }

  private function tokenFor(User $user): array
  {
    return ['Authorization' => 'Bearer ' . $user->createToken('postman')->plainTextToken];
  }

  /**
   * @return array<string, mixed>
   */
  private function payload(PerangkatDaerah $pd, array $override = []): array
  {
    return array_merge([
      'perangkat_daerah_id' => $pd->id,
      'nomor_polisi' => 'B 1234 ABC',
      'merk' => 'Toyota',
      'tipe_model' => 'Avanza',
      'tahun_produksi' => 2020,
      'warna' => 'Hitam',
      'jenis_kendaraan' => 'Mobil',
      'bahan_bakar' => 'Bensin',
      'kondisi' => 'Baik',
      'status_penggunaan' => 'Digunakan',
      'tanggal_perolehan' => '2020-01-15',
      'nilai_perolehan' => 250000000,
    ], $override);
  }

  public function test_admin_dapat_melihat_daftar_kendaraan(): void
  {
    $pd = $this->perangkatDaerah();

    Kendaraan::create($this->payload($pd));

    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/kendaraan')
      ->assertOk()
      ->assertJsonStructure([
        'data' => [
          '*' => ['id', 'nomor_polisi', 'merk', 'kondisi'],
        ],
        'links',
        'meta',
      ])
      ->assertJsonPath('data.0.nomor_polisi', 'B 1234 ABC');
  }

  public function test_admin_dapat_menambah_kendaraan(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->postJson('/api/kendaraan', $this->payload($pd, [
        'pegawai_id' => Pegawai::create([
          'perangkat_daerah_id' => $pd->id,
          'nama' => 'Budi',
          'nip' => '198701012010011001',
          'status' => 'Aktif',
        ])->id,
      ]))
      ->assertStatus(201)
      ->assertJsonPath('message', 'Data kendaraan berhasil ditambahkan.')
      ->assertJsonPath('data.nomor_polisi', 'B 1234 ABC')
      ->assertJsonPath('data.pegawai.nama', 'Budi')
      ->assertJsonPath('data.perangkat_daerah.kode_pd', '1.01.01');

    $this->assertDatabaseHas('kendaraan', [
      'nomor_polisi' => 'B 1234 ABC',
      'merk' => 'Toyota',
    ]);
  }

  public function test_store_gagal_karena_validasi(): void
  {
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->postJson('/api/kendaraan', [])
      ->assertStatus(422)
      ->assertJsonValidationErrors([
        'perangkat_daerah_id',
        'nomor_polisi',
        'merk',
        'jenis_kendaraan',
        'kondisi',
        'status_penggunaan',
      ]);
  }

  public function test_store_menolak_nomor_polisi_duplikat(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    Kendaraan::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->postJson('/api/kendaraan', $this->payload($pd))
      ->assertStatus(422)
      ->assertJsonValidationErrors(['nomor_polisi']);
  }

  public function test_operator_dipaksa_memakai_perangkat_daerah_sendiri(): void
  {
    $milikSendiri = $this->perangkatDaerah('1.01.01');
    $lain = $this->perangkatDaerah('1.01.02');

    $operator = User::factory()->create([
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);

    $this->withHeaders($this->tokenFor($operator))
      ->postJson('/api/kendaraan', $this->payload($lain))
      ->assertStatus(201)
      ->assertJsonPath('data.perangkat_daerah_id', $milikSendiri->id);

    $this->assertDatabaseHas('kendaraan', [
      'nomor_polisi' => 'B 1234 ABC',
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);
  }

  public function test_operator_tidak_melihat_kendaraan_pd_lain(): void
  {
    $milikSendiri = $this->perangkatDaerah('1.01.01');
    $lain = $this->perangkatDaerah('1.01.02');

    Kendaraan::create($this->payload($milikSendiri));
    Kendaraan::create($this->payload($lain, ['nomor_polisi' => 'B 9999 ZZZ']));

    $operator = User::factory()->create([
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);

    $this->withHeaders($this->tokenFor($operator))
      ->getJson('/api/kendaraan')
      ->assertOk()
      ->assertJsonCount(1, 'data')
      ->assertJsonPath('data.0.nomor_polisi', 'B 1234 ABC');
  }

  public function test_operator_tidak_dapat_mengakses_kendaraan_pd_lain(): void
  {
    $milikSendiri = $this->perangkatDaerah('1.01.01');
    $lain = $this->perangkatDaerah('1.01.02');

    $kendaraanLain = Kendaraan::create(
      $this->payload($lain)
    );

    $operator = User::factory()->create([
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);

    $this->withHeaders($this->tokenFor($operator))
      ->getJson('/api/kendaraan/' . $kendaraanLain->id)
      ->assertStatus(403);
  }

  public function test_admin_dapat_menampilkan_detail_kendaraan(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $kendaraan = Kendaraan::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/kendaraan/' . $kendaraan->id)
      ->assertOk()
      ->assertJsonPath('data.id', $kendaraan->id)
      ->assertJsonPath('data.tanggal_perolehan', '2020-01-15');
  }

  public function test_admin_dapat_memperbarui_kendaraan(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $kendaraan = Kendaraan::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->putJson('/api/kendaraan/' . $kendaraan->id, $this->payload($pd, [
        'merk' => 'Daihatsu',
        'kondisi' => 'Rusak Ringan',
      ]))
      ->assertOk()
      ->assertJsonPath('message', 'Data kendaraan berhasil diperbarui.')
      ->assertJsonPath('data.merk', 'Daihatsu')
      ->assertJsonPath('data.kondisi', 'Rusak Ringan');

    $this->assertDatabaseHas('kendaraan', [
      'id' => $kendaraan->id,
      'merk' => 'Daihatsu',
      'kondisi' => 'Rusak Ringan',
    ]);
  }

  public function test_update_mengizinkan_nomor_polisi_sendiri(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $kendaraan = Kendaraan::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->putJson('/api/kendaraan/' . $kendaraan->id, $this->payload($pd))
      ->assertOk()
      ->assertJsonPath('data.nomor_polisi', 'B 1234 ABC');
  }

  public function test_admin_dapat_menghapus_kendaraan(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $kendaraan = Kendaraan::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->deleteJson('/api/kendaraan/' . $kendaraan->id)
      ->assertOk()
      ->assertJsonPath('message', 'Data kendaraan berhasil dihapus.');

    $this->assertSoftDeleted('kendaraan', ['id' => $kendaraan->id]);
  }

  public function test_filter_search_dan_kondisi(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    Kendaraan::create($this->payload($pd));
    Kendaraan::create($this->payload($pd, [
      'nomor_polisi' => 'B 5678 DEF',
      'merk' => 'Honda',
      'kondisi' => 'Rusak Berat',
    ]));

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/kendaraan?kondisi=Rusak%20Berat')
      ->assertOk()
      ->assertJsonCount(1, 'data')
      ->assertJsonPath('data.0.nomor_polisi', 'B 5678 DEF');

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/kendaraan?search=Honda')
      ->assertOk()
      ->assertJsonCount(1, 'data')
      ->assertJsonPath('data.0.merk', 'Honda');
  }

  public function test_kendaraan_tidak_ditemukan_mengembalikan_404(): void
  {
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/kendaraan/999999')
      ->assertStatus(404);
  }
}
