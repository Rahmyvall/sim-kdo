<?php

namespace Database\Seeders;

use App\Models\PerangkatDaerah;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $perangkatDaerah = PerangkatDaerah::firstOrCreate(
            ['kode_pd' => '1.01.01'],
            [
                'nama_pd' => 'Dinas Pendidikan',
                'nama_kepala_pd' => 'Kepala Dinas Pendidikan',
                'no_telepon' => '021-1234567',
                'status' => 'Aktif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@sim-kdo.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'perangkat_daerah_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'operator@sim-kdo.test'],
            [
                'name' => 'Operator Dinas Pendidikan',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'perangkat_daerah_id' => $perangkatDaerah->id,
            ]
        );
    }
}
