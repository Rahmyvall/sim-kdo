<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->currentUser();

        $query = Kendaraan::with([
            'pegawai',
            'perangkatDaerah'
        ]);

        if (! $user->isAdmin()) {
            $query->where('perangkat_daerah_id', $user->perangkat_daerah_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($sub) use ($search) {
                $sub->where('nomor_polisi', 'like', '%' . $search . '%')
                    ->orWhere('merk', 'like', '%' . $search . '%')
                    ->orWhere('tipe_model', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $kendaraan = $query->latest()->paginate(10);

        return view('kendaraan.index', compact('kendaraan'));
    }

    public function show(Kendaraan $kendaraan)
    {
        $this->authorizeAccess($kendaraan);

        $kendaraan->load(['pegawai', 'perangkatDaerah']);

        return view('kendaraan.show', compact('kendaraan'));
    }

    public function create()
    {
        $perangkatDaerah = PerangkatDaerah::all();
        $pegawai = Pegawai::where('status', 'Aktif')->get();

        return view('kendaraan.create', compact(
            'perangkatDaerah',
            'pegawai'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'nomor_polisi' => 'required|unique:kendaraan,nomor_polisi',
            'merk' => 'required|max:100',
            'tipe_model' => 'nullable|max:100',
            'tahun_produksi' => 'nullable|integer',
            'warna' => 'nullable|max:40',
            'jenis_kendaraan' => 'required|max:60',
            'kondisi' => [
                'required',
                Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat'])
            ],
            'status_penggunaan' => [
                'required',
                Rule::in(['Digunakan', 'Tidak Digunakan', 'Dalam Perawatan', 'Dihapuskan'])
            ],
        ]);

        $user = $this->currentUser();

        if (! $user->isAdmin()) {
            $data['perangkat_daerah_id'] = $user->perangkat_daerah_id;
        }

        Kendaraan::create($data);

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan)
    {
        $this->authorizeAccess($kendaraan);

        $perangkatDaerah = PerangkatDaerah::all();
        $pegawai = Pegawai::all();

        return view('kendaraan.edit', compact(
            'kendaraan',
            'perangkatDaerah',
            'pegawai'
        ));
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $this->authorizeAccess($kendaraan);

        $data = $request->validate([
            'nomor_polisi' => [
                'required',
                Rule::unique('kendaraan')->ignore($kendaraan->id)
            ],
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'merk' => 'required|max:100',
            'tipe_model' => 'nullable|max:100',
            'tahun_produksi' => 'nullable|integer',
            'warna' => 'nullable|max:40',
            'jenis_kendaraan' => 'required|max:60',
            'kondisi' => [
                'required',
                Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat'])
            ],
            'status_penggunaan' => [
                'required',
                Rule::in(['Digunakan', 'Tidak Digunakan', 'Dalam Perawatan', 'Dihapuskan'])
            ],
        ]);

        $user = $this->currentUser();

        if (! $user->isAdmin()) {
            $data['perangkat_daerah_id'] = $user->perangkat_daerah_id;
        }

        $kendaraan->update($data);

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $this->authorizeAccess($kendaraan);

        $kendaraan->delete();

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus.');
    }

    private function authorizeAccess(Kendaraan $kendaraan): void
    {
        $user = $this->currentUser();

        abort_unless(
            $user->isAdmin() ||
                $kendaraan->perangkat_daerah_id == $user->perangkat_daerah_id,
            403
        );
    }
}
