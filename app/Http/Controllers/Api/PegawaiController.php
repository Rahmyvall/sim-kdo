<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StorePegawaiRequest;
use App\Http\Requests\Api\UpdatePegawaiRequest;
use App\Http\Resources\PegawaiResource;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PegawaiController extends ApiController
{
  /**
   * Daftar pegawai.
   *
   * GET /api/pegawai
   */
  public function index(Request $request): AnonymousResourceCollection
  {
    $user = $this->currentUser();

    $query = Pegawai::with('perangkatDaerah');

    if (! $user->isAdmin()) {
      $query->where('perangkat_daerah_id', $user->perangkat_daerah_id);
    }

    if ($request->filled('search')) {
      $search = $request->string('search')->toString();

      $query->where(function ($sub) use ($search) {
        $sub->where('nama', 'like', '%' . $search . '%')
          ->orWhere('nip', 'like', '%' . $search . '%')
          ->orWhere('jabatan', 'like', '%' . $search . '%');
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->string('status')->toString());
    }

    if ($request->filled('perangkat_daerah_id')) {
      $query->where(
        'perangkat_daerah_id',
        $request->integer('perangkat_daerah_id')
      );
    }

    $pegawai = $query->latest()->paginate($this->perPage($request));

    return PegawaiResource::collection($pegawai);
  }

  /**
   * Simpan pegawai baru.
   *
   * POST /api/pegawai
   */
  public function store(StorePegawaiRequest $request): JsonResponse
  {
    $data = $this->applyOwnershipScope($request->validated());

    $pegawai = Pegawai::create($data);

    $pegawai->load('perangkatDaerah');

    return response()->json([
      'message' => 'Data pegawai berhasil ditambahkan.',
      'data' => PegawaiResource::make($pegawai),
    ], 201);
  }

  /**
   * Detail pegawai.
   *
   * GET /api/pegawai/{pegawai}
   */
  public function show(Pegawai $pegawai): JsonResponse
  {
    $this->authorizeOwnership($pegawai);

    $pegawai->load(['perangkatDaerah', 'kendaraan']);

    return response()->json([
      'data' => PegawaiResource::make($pegawai),
    ]);
  }

  /**
   * Perbarui data pegawai.
   *
   * PUT/PATCH /api/pegawai/{pegawai}
   */
  public function update(UpdatePegawaiRequest $request, Pegawai $pegawai): JsonResponse
  {
    $this->authorizeOwnership($pegawai);

    $data = $this->applyOwnershipScope($request->validated());

    $pegawai->update($data);

    $pegawai->load('perangkatDaerah');

    return response()->json([
      'message' => 'Data pegawai berhasil diperbarui.',
      'data' => PegawaiResource::make($pegawai),
    ]);
  }

  /**
   * Hapus pegawai.
   *
   * DELETE /api/pegawai/{pegawai}
   */
  public function destroy(Pegawai $pegawai): JsonResponse
  {
    $this->authorizeOwnership($pegawai);

    $pegawai->delete();

    return response()->json([
      'message' => 'Data pegawai berhasil dihapus.',
    ]);
  }
}
