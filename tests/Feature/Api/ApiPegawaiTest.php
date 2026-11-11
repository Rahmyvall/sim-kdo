<?php

namespace Tests\Feature\Api;

use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPegawaiTest extends TestCase
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

  /**
   * @return array<string, string>
   */
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
      'nama' => 'Budi Santoso',
      'nip' => '198701012010011001',
      'jabatan' => 'Kepala Seksi',
      'golongan' => 'III/c',
      'pangkat' => 'Penata',
      'no_telepon' => '081234567890',
      'status' => 'Aktif',
    ], $override);
  }

  public function test_admin_dapat_melihat_daftar_pegawai(): void
  {
    $pd = $this->perangkatDaerah();
    Pegawai::create($this->payload($pd));

    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/pegawai')
      ->assertOk()
      ->assertJsonPath('data.0.nama', 'Budi Santoso')
      ->assertJsonPath('data.0.perangkat_daerah.kode_pd', '1.01.01')
      ->assertJsonStructure(['data', 'links', 'meta']);
  }

  public function test_admin_dapat_menambah_pegawai(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->postJson('/api/pegawai', $this->payload($pd))
      ->assertStatus(201)
      ->assertJsonPath('message', 'Data pegawai berhasil ditambahkan.')
      ->assertJsonPath('data.nip', '198701012010011001');

    $this->assertDatabaseHas('pegawai', [
      'nip' => '198701012010011001',
      'nama' => 'Budi Santoso',
    ]);
  }

  public function test_store_gagal_karena_validasi(): void
  {
    $admin = User::factory()->admin()->create();

    $this->withHeaders($this->tokenFor($admin))
      ->postJson('/api/pegawai', [])
      ->assertStatus(422)
      ->assertJsonValidationErrors([
        'perangkat_daerah_id',
        'nama',
        'nip',
        'status',
      ]);
  }

  public function test_store_menolak_nip_duplikat(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    Pegawai::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->postJson('/api/pegawai', $this->payload($pd, ['nama' => 'Orang Lain']))
      ->assertStatus(422)
      ->assertJsonValidationErrors(['nip']);
  }

  public function test_operator_dipaksa_memakai_perangkat_daerah_sendiri(): void
  {
    $milikSendiri = $this->perangkatDaerah('1.01.01');
    $lain = $this->perangkatDaerah('1.01.02');

    $operator = User::factory()->create([
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);

    $this->withHeaders($this->tokenFor($operator))
      ->postJson('/api/pegawai', $this->payload($lain))
      ->assertStatus(201)
      ->assertJsonPath('data.perangkat_daerah_id', $milikSendiri->id);
  }

  public function test_operator_tidak_melihat_pegawai_pd_lain(): void
  {
    $milikSendiri = $this->perangkatDaerah('1.01.01');
    $lain = $this->perangkatDaerah('1.01.02');

    Pegawai::create($this->payload($milikSendiri));
    Pegawai::create($this->payload($lain, [
      'nip' => '199001012015011002',
      'nama' => 'Pegawai Lain',
    ]));

    $operator = User::factory()->create([
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);

    $this->withHeaders($this->tokenFor($operator))
      ->getJson('/api/pegawai')
      ->assertOk()
      ->assertJsonCount(1, 'data')
      ->assertJsonPath('data.0.nama', 'Budi Santoso');
  }

  public function test_admin_dapat_menampilkan_detail_pegawai(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $pegawai = Pegawai::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/pegawai/' . $pegawai->id)
      ->assertOk()
      ->assertJsonPath('data.id', $pegawai->id)
      ->assertJsonPath('data.jabatan', 'Kepala Seksi');
  }

  public function test_admin_dapat_memperbarui_pegawai(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $pegawai = Pegawai::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->putJson('/api/pegawai/' . $pegawai->id, $this->payload($pd, [
        'jabatan' => 'Sekretaris',
        'status' => 'Tidak Aktif',
      ]))
      ->assertOk()
      ->assertJsonPath('message', 'Data pegawai berhasil diperbarui.')
      ->assertJsonPath('data.jabatan', 'Sekretaris')
      ->assertJsonPath('data.status', 'Tidak Aktif');

    $this->assertDatabaseHas('pegawai', [
      'id' => $pegawai->id,
      'jabatan' => 'Sekretaris',
    ]);
  }

  public function test_operator_tidak_dapat_mengubah_pegawai_pd_lain(): void
  {
    $milikSendiri = $this->perangkatDaerah('1.01.01');
    $lain = $this->perangkatDaerah('1.01.02');

    $pegawaiLain = Pegawai::create($this->payload($lain));

    $operator = User::factory()->create([
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);

    $this->withHeaders($this->tokenFor($operator))
      ->deleteJson('/api/pegawai/' . $pegawaiLain->id)
      ->assertStatus(403);

    $this->assertDatabaseHas('pegawai', [
      'id' => $pegawaiLain->id,
      'deleted_at' => null,
    ]);
  }

  public function test_admin_dapat_menghapus_pegawai(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    $pegawai = Pegawai::create($this->payload($pd));

    $this->withHeaders($this->tokenFor($admin))
      ->deleteJson('/api/pegawai/' . $pegawai->id)
      ->assertOk()
      ->assertJsonPath('message', 'Data pegawai berhasil dihapus.');

    $this->assertSoftDeleted('pegawai', ['id' => $pegawai->id]);
  }

  public function test_filter_search_dan_status(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = User::factory()->admin()->create();

    Pegawai::create($this->payload($pd));
    Pegawai::create($this->payload($pd, [
      'nip' => '199001012015011002',
      'nama' => 'Siti Aminah',
      'status' => 'Tidak Aktif',
    ]));

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/pegawai?search=Siti')
      ->assertOk()
      ->assertJsonCount(1, 'data')
      ->assertJsonPath('data.0.nama', 'Siti Aminah');

    $this->withHeaders($this->tokenFor($admin))
      ->getJson('/api/pegawai?status=Tidak%20Aktif')
      ->assertOk()
      ->assertJsonCount(1, 'data')
      ->assertJsonPath('data.0.nama', 'Siti Aminah');
  }
}
