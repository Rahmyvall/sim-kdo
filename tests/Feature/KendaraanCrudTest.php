<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KendaraanCrudTest extends TestCase
{
  use RefreshDatabase;

  private function admin(): User
  {
    return User::factory()->admin()->create();
  }

  private function perangkatDaerah(): PerangkatDaerah
  {
    return PerangkatDaerah::create([
      'kode_pd' => '1.01.01',
      'nama_pd' => 'Dinas Pendidikan',
      'status' => 'Aktif',
    ]);
  }

  public function test_admin_dapat_menambah_kendaraan(): void
  {
    $pd = $this->perangkatDaerah();

    $this->actingAs($this->admin())
      ->post('/kendaraan', [
        'perangkat_daerah_id' => $pd->id,
        'nomor_polisi' => 'B 1234 ABC',
        'merk' => 'Toyota',
        'tipe_model' => 'Avanza',
        'jenis_kendaraan' => 'Mobil',
        'kondisi' => 'Baik',
        'status_penggunaan' => 'Digunakan',
      ])
      ->assertRedirect('/kendaraan');

    $this->assertDatabaseHas('kendaraan', [
      'nomor_polisi' => 'B 1234 ABC',
      'merk' => 'Toyota',
      'jenis_kendaraan' => 'Mobil',
    ]);
  }

  public function test_operator_dipaksa_memakai_perangkat_daerah_sendiri(): void
  {
    $milikSendiri = $this->perangkatDaerah();

    $lain = PerangkatDaerah::create([
      'kode_pd' => '1.01.02',
      'nama_pd' => 'Dinas Kesehatan',
      'status' => 'Aktif',
    ]);

    $operator = User::factory()->create([
      'role' => 'operator',
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);

    $this->actingAs($operator)
      ->post('/kendaraan', [
        'perangkat_daerah_id' => $lain->id,
        'nomor_polisi' => 'B 5555 XYZ',
        'merk' => 'Honda',
        'jenis_kendaraan' => 'Mobil',
        'kondisi' => 'Baik',
        'status_penggunaan' => 'Digunakan',
      ]);

    $this->assertDatabaseHas('kendaraan', [
      'nomor_polisi' => 'B 5555 XYZ',
      'perangkat_daerah_id' => $milikSendiri->id,
    ]);
  }

  public function test_detail_dan_update_kendaraan(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = $this->admin();

    $kendaraan = Kendaraan::create([
      'perangkat_daerah_id' => $pd->id,
      'nomor_polisi' => 'B 7777 QWE',
      'merk' => 'Suzuki',
      'jenis_kendaraan' => 'Mobil',
      'kondisi' => 'Baik',
      'status_penggunaan' => 'Digunakan',
    ]);

    $this->actingAs($admin)
      ->get('/kendaraan/' . $kendaraan->id)
      ->assertOk()
      ->assertSee('B 7777 QWE');

    $this->put('/kendaraan/' . $kendaraan->id, [
      'perangkat_daerah_id' => $pd->id,
      'nomor_polisi' => 'B 7777 QWE',
      'merk' => 'Suzuki',
      'tipe_model' => 'Ertiga',
      'jenis_kendaraan' => 'Mobil',
      'kondisi' => 'Rusak Ringan',
      'status_penggunaan' => 'Dalam Perawatan',
    ])->assertRedirect('/kendaraan');

    $this->assertDatabaseHas('kendaraan', [
      'id' => $kendaraan->id,
      'tipe_model' => 'Ertiga',
      'kondisi' => 'Rusak Ringan',
      'status_penggunaan' => 'Dalam Perawatan',
    ]);
  }

  public function test_operator_tidak_boleh_mengubah_kendaraan_milik_pd_lain(): void
  {
    $pdLain = $this->perangkatDaerah();

    $kendaraan = Kendaraan::create([
      'perangkat_daerah_id' => $pdLain->id,
      'nomor_polisi' => 'B 8888 QWE',
      'merk' => 'Daihatsu',
      'kondisi' => 'Baik',
      'status_penggunaan' => 'Digunakan',
    ]);

    $operator = User::factory()->create([
      'role' => 'operator',
      'perangkat_daerah_id' => null,
    ]);

    $this->actingAs($operator)
      ->get('/kendaraan/' . $kendaraan->id)
      ->assertForbidden();
  }

  public function test_admin_dapat_menghapus_kendaraan(): void
  {
    $pd = $this->perangkatDaerah();

    $kendaraan = Kendaraan::create([
      'perangkat_daerah_id' => $pd->id,
      'nomor_polisi' => 'B 1111 ZZZ',
      'merk' => 'Nissan',
      'kondisi' => 'Baik',
      'status_penggunaan' => 'Digunakan',
    ]);

    $this->actingAs($this->admin())
      ->delete('/kendaraan/' . $kendaraan->id)
      ->assertRedirect('/kendaraan');

    $this->assertSoftDeleted('kendaraan', ['id' => $kendaraan->id]);
  }

  public function test_admin_dapat_menambah_dan_menghapus_pegawai(): void
  {
    $pd = $this->perangkatDaerah();
    $admin = $this->admin();

    $this->actingAs($admin)
      ->post('/pegawai', [
        'perangkat_daerah_id' => $pd->id,
        'nama' => 'Budi Santoso',
        'nip' => '198001012005011001',
        'jabatan' => 'Kepala Seksi',
        'status' => 'Aktif',
      ])
      ->assertRedirect('/pegawai');

    $pegawai = Pegawai::where('nip', '198001012005011001')->firstOrFail();

    $this->get('/pegawai/' . $pegawai->id)
      ->assertOk()
      ->assertSee('Budi Santoso');

    $this->delete('/pegawai/' . $pegawai->id)
      ->assertRedirect('/pegawai');

    $this->assertSoftDeleted('pegawai', ['id' => $pegawai->id]);
  }

  public function test_admin_dapat_mengelola_perangkat_daerah(): void
  {
    $admin = $this->admin();

    $this->actingAs($admin)
      ->post('/perangkat-daerah', [
        'kode_pd' => '2.02.02',
        'nama_pd' => 'Dinas Perhubungan',
        'status' => 'Aktif',
      ])
      ->assertRedirect('/perangkat-daerah');

    $pd = PerangkatDaerah::where('kode_pd', '2.02.02')->firstOrFail();

    $this->get('/perangkat-daerah/' . $pd->id)
      ->assertOk()
      ->assertSee('Dinas Perhubungan');

    $this->delete('/perangkat-daerah/' . $pd->id)
      ->assertRedirect('/perangkat-daerah');

    $this->assertSoftDeleted('perangkat_daerah', ['id' => $pd->id]);
  }

  public function test_perangkat_daerah_dengan_pegawai_tidak_bisa_dihapus(): void
  {
    $pd = $this->perangkatDaerah();

    Pegawai::create([
      'perangkat_daerah_id' => $pd->id,
      'nama' => 'Siti Aminah',
      'nip' => '199002022010012002',
      'status' => 'Aktif',
    ]);

    $this->actingAs($this->admin())
      ->delete('/perangkat-daerah/' . $pd->id)
      ->assertRedirect('/perangkat-daerah')
      ->assertSessionHas('error');

    $this->assertDatabaseHas('perangkat_daerah', [
      'id' => $pd->id,
      'deleted_at' => null,
    ]);
  }
}
