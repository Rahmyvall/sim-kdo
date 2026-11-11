<?php

namespace App\Http\Controllers;

use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerangkatDaerahController extends Controller
{
    /**
     * Daftar perangkat daerah.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = PerangkatDaerah::withCount(['pegawai', 'kendaraan']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($sub) use ($search) {
                $sub->where('nama_pd', 'like', '%' . $search . '%')
                    ->orWhere('kode_pd', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perangkatDaerah = $query->orderBy('nama_pd')->paginate(10);

        return view(
            'perangkat-daerah.index',
            compact('perangkatDaerah')
        );
    }

    /**
     * Form tambah perangkat daerah.
     */
    public function create()
    {
        $this->authorizeAdmin();

        return view('perangkat-daerah.create');
    }

    /**
     * Simpan perangkat daerah baru.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'kode_pd' => 'required|max:30|unique:perangkat_daerah,kode_pd',
            'nama_pd' => 'required|max:150',
            'alamat' => 'nullable|max:255',
            'nama_kepala_pd' => 'nullable|max:100',
            'no_telepon' => 'nullable|max:20',
            'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
        ]);

        PerangkatDaerah::create($data);

        return redirect()
            ->route('perangkat-daerah.index')
            ->with('success', 'Perangkat daerah berhasil ditambahkan.');
    }

    /**
     * Detail perangkat daerah.
     */
    public function show(PerangkatDaerah $perangkatDaerah)
    {
        $this->authorizeAdmin();

        $perangkatDaerah->load(['pegawai', 'kendaraan']);

        return view(
            'perangkat-daerah.show',
            compact('perangkatDaerah')
        );
    }

    /**
     * Form edit perangkat daerah.
     */
    public function edit(PerangkatDaerah $perangkatDaerah)
    {
        $this->authorizeAdmin();

        return view(
            'perangkat-daerah.edit',
            compact('perangkatDaerah')
        );
    }

    /**
     * Perbarui perangkat daerah.
     */
    public function update(
        Request $request,
        PerangkatDaerah $perangkatDaerah
    ) {
        $this->authorizeAdmin();

        $data = $request->validate([
            'kode_pd' => [
                'required',
                'max:30',
                Rule::unique('perangkat_daerah', 'kode_pd')
                    ->ignore($perangkatDaerah->id),
            ],
            'nama_pd' => 'required|max:150',
            'alamat' => 'nullable|max:255',
            'nama_kepala_pd' => 'nullable|max:100',
            'no_telepon' => 'nullable|max:20',
            'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
        ]);

        $perangkatDaerah->update($data);

        return redirect()
            ->route('perangkat-daerah.index')
            ->with('success', 'Perangkat daerah berhasil diperbarui.');
    }

    /**
     * Hapus perangkat daerah.
     */
    public function destroy(PerangkatDaerah $perangkatDaerah)
    {
        $this->authorizeAdmin();

        if (
            $perangkatDaerah->pegawai()->exists() ||
            $perangkatDaerah->kendaraan()->exists()
        ) {
            return redirect()
                ->route('perangkat-daerah.index')
                ->with(
                    'error',
                    'Perangkat daerah masih memiliki data pegawai/kendaraan.'
                );
        }

        $perangkatDaerah->delete();

        return redirect()
            ->route('perangkat-daerah.index')
            ->with('success', 'Perangkat daerah berhasil dihapus.');
    }

    /**
     * Hanya admin yang boleh mengelola master data.
     */
    private function authorizeAdmin(): void
    {
        abort_unless($this->currentUser()->isAdmin(), 403);
    }
}
