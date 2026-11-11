<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_dibuka(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk');
    }

    public function test_login_gagal_dengan_kredensial_salah(): void
    {
        $this->post('/login', [
            'email' => 'salah@example.com',
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_tamu_diarahkan_ke_login_saat_membuka_dashboard(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_admin_dapat_membuka_seluruh_halaman(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/')
            ->assertOk()
            ->assertSee('Dashboard SIM-KDO');

        $this->get('/kendaraan')->assertOk();
        $this->get('/kendaraan/create')->assertOk();
        $this->get('/pegawai')->assertOk();
        $this->get('/pegawai/create')->assertOk();
        $this->get('/perangkat-daerah')->assertOk();
        $this->get('/perangkat-daerah/create')->assertOk();
    }

    public function test_operator_tidak_dapat_membuka_menu_perangkat_daerah(): void
    {
        $operator = User::factory()->create();

        $this->actingAs($operator)
            ->get('/perangkat-daerah')
            ->assertForbidden();
    }

    public function test_logout_mengakhiri_sesi(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
