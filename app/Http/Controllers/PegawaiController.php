<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    /**
     * Daftar pegawai.
     */
    public function index(Request $request)
    {
        $user = $this->currentUser();

        $query = Pegawai::with('perangkatDaerah');

        if (! $user->isAdmin()) {
            $query->where('perangkat_daerah_id', $user->perangkat_daerah_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pegawai = $query->latest()->paginate(10);

        return view('pegawai.index', compact('pegawai'));
    }

    /**
     * Form tambah pegawai.
     */
    public function create()
    {
        $perangkatDaerah = PerangkatDaerah::orderBy('nama_pd')->get();

        return view('pegawai.create', compact('perangkatDaerah'));
    }

    /**
     * Simpan pegawai baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'nama' => 'required|max:100',
            'nip' => 'required|max:30|unique:pegawai,nip',
            'jabatan' => 'nullable|max:100',
            'golongan' => 'nullable|max:20',
            'pangkat' => 'nullable|max:50',
            'no_telepon' => 'nullable|max:20',
            'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
        ]);

        $user = $this->currentUser();

        if (! $user->isAdmin()) {
            $data['perangkat_daerah_id'] = $user->perangkat_daerah_id;
        }

        Pegawai::create($data);

        return redirect()
            ->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    /**
     * Detail pegawai.
     */
    public function show(Pegawai $pegawai)
    {
        $this->authorizeAccess($pegawai);

        $pegawai->load('perangkatDaerah');
        $pegawai->load('kendaraan');

        return view('pegawai.show', compact('pegawai'));
    }

    /**
     * Form edit pegawai.
     */
    public function edit(Pegawai $pegawai)
    {
        $this->authorizeAccess($pegawai);

        $perangkatDaerah = PerangkatDaerah::orderBy('nama_pd')->get();

        return view('pegawai.edit', compact('pegawai', 'perangkatDaerah'));
    }

    /**
     * Perbarui data pegawai.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $this->authorizeAccess($pegawai);

        $data = $request->validate([
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'nama' => 'required|max:100',
            'nip' => [
                'required',
                'max:30',
                Rule::unique('pegawai', 'nip')->ignore($pegawai->id),
            ],
            'jabatan' => 'nullable|max:100',
            'golongan' => 'nullable|max:20',
            'pangkat' => 'nullable|max:50',
            'no_telepon' => 'nullable|max:20',
            'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
        ]);

        $user = $this->currentUser();

        if (! $user->isAdmin()) {
            $data['perangkat_daerah_id'] = $user->perangkat_daerah_id;
        }

        $pegawai->update($data);

        return redirect()
            ->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Hapus pegawai.
     */
    public function destroy(Pegawai $pegawai)
    {
        $this->authorizeAccess($pegawai);

        $pegawai->delete();

        return redirect()
            ->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }

    /**
     * Batasi akses pegawai sesuai perangkat daerah user.
     */
    private function authorizeAccess(Pegawai $pegawai): void
    {
        $user = $this->currentUser();

        abort_unless(
            $user->isAdmin() ||
                $pegawai->perangkat_daerah_id == $user->perangkat_daerah_id,
            403
        );
    }
}
