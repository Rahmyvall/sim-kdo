<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreKendaraanRequest;
use App\Http\Requests\Api\UpdateKendaraanRequest;
use App\Http\Resources\KendaraanResource;
use App\Models\Kendaraan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KendaraanController extends ApiController
{
  /**
   * Daftar kendaraan.
   *
   * GET /api/kendaraan
   */
  public function index(Request $request): AnonymousResourceCollection
  {
    $user = $this->currentUser();

    $query = Kendaraan::with(['pegawai', 'perangkatDaerah']);

    if (! $user->isAdmin()) {
      $query->where('perangkat_daerah_id', $user->perangkat_daerah_id);
    }

    if ($request->filled('search')) {
      $search = $request->string('search')->toString();

      $query->where(function ($sub) use ($search) {
        $sub->where('nomor_polisi', 'like', '%' . $search . '%')
          ->orWhere('merk', 'like', '%' . $search . '%')
          ->orWhere('tipe_model', 'like', '%' . $search . '%');
      });
    }

    if ($request->filled('kondisi')) {
      $query->where('kondisi', $request->string('kondisi')->toString());
    }

    if ($request->filled('status_penggunaan')) {
      $query->where(
        'status_penggunaan',
        $request->string('status_penggunaan')->toString()
      );
    }

    if ($request->filled('perangkat_daerah_id')) {
      $query->where(
        'perangkat_daerah_id',
        $request->integer('perangkat_daerah_id')
      );
    }

    $kendaraan = $query->latest()->paginate($this->perPage($request));

    return KendaraanResource::collection($kendaraan);
  }

  /**
   * Simpan kendaraan baru.
   *
   * POST /api/kendaraan
   */
  public function store(StoreKendaraanRequest $request): JsonResponse
  {
    $data = $this->applyOwnershipScope($request->validated());

    $kendaraan = Kendaraan::create($data);

    $kendaraan->load(['pegawai', 'perangkatDaerah']);

    return response()->json([
      'message' => 'Data kendaraan berhasil ditambahkan.',
      'data' => KendaraanResource::make($kendaraan),
    ], 201);
  }

  /**
   * Detail kendaraan.
   *
   * GET /api/kendaraan/{kendaraan}
   */
  public function show(Kendaraan $kendaraan): JsonResponse
  {
    $this->authorizeOwnership($kendaraan);

    $kendaraan->load(['pegawai', 'perangkatDaerah']);

    return response()->json([
      'data' => KendaraanResource::make($kendaraan),
    ]);
  }

  /**
   * Perbarui data kendaraan.
   *
   * PUT/PATCH /api/kendaraan/{kendaraan}
   */
  public function update(UpdateKendaraanRequest $request, Kendaraan $kendaraan): JsonResponse
  {
    $this->authorizeOwnership($kendaraan);

    $data = $this->applyOwnershipScope($request->validated());

    $kendaraan->update($data);

    $kendaraan->load(['pegawai', 'perangkatDaerah']);

    return response()->json([
      'message' => 'Data kendaraan berhasil diperbarui.',
      'data' => KendaraanResource::make($kendaraan),
    ]);
  }

  /**
   * Hapus kendaraan.
   *
   * DELETE /api/kendaraan/{kendaraan}
   */
  public function destroy(Kendaraan $kendaraan): JsonResponse
  {
    $this->authorizeOwnership($kendaraan);

    $kendaraan->delete();

    return response()->json([
      'message' => 'Data kendaraan berhasil dihapus.',
    ]);
  }
}
