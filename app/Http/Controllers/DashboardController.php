<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan data kendaraan.
     */
    public function index()
    {
        $user = $this->currentUser();

        $baseQuery = function () use ($user) {
            $query = Kendaraan::query();

            if (! $user->isAdmin()) {
                $query->where('perangkat_daerah_id', $user->perangkat_daerah_id);
            }

            return $query;
        };

        $totalKendaraan = $baseQuery()->count();

        $baik = $baseQuery()->where('kondisi', 'Baik')->count();

        $rusakRingan = $baseQuery()->where('kondisi', 'Rusak Ringan')->count();

        $rusakBerat = $baseQuery()->where('kondisi', 'Rusak Berat')->count();

        return view('dashboard.index', compact(
            'totalKendaraan',
            'baik',
            'rusakRingan',
            'rusakBerat'
        ));
    }
}
