<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StorePerangkatDaerahRequest;
use App\Http\Requests\Api\UpdatePerangkatDaerahRequest;
use App\Http\Resources\PerangkatDaerahResource;
use App\Models\PerangkatDaerah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PerangkatDaerahController extends ApiController
{
  /**
   * Daftar perangkat daerah (khusus admin).
   *
   * GET /api/perangkat-daerah
   */
  public function index(Request $request): AnonymousResourceCollection
  {
    $this->authorizeAdmin();

    $query = PerangkatDaerah::withCount(['pegawai', 'kendaraan']);

    if ($request->filled('search')) {
      $search = $request->string('search')->toString();

      $query->where(function ($sub) use ($search) {
        $sub->where('nama_pd', 'like', '%' . $search . '%')
          ->orWhere('kode_pd', 'like', '%' . $search . '%');
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->string('status')->toString());
    }

    $perangkatDaerah = $query->orderBy('nama_pd')->paginate($this->perPage($request));

    return PerangkatDaerahResource::collection($perangkatDaerah);
  }

  /**
   * Simpan perangkat daerah baru (khusus admin).
   *
   * POST /api/perangkat-daerah
   */
  public function store(StorePerangkatDaerahRequest $request): JsonResponse
  {
    $perangkatDaerah = PerangkatDaerah::create($request->validated());

    return response()->json([
      'message' => 'Perangkat daerah berhasil ditambahkan.',
      'data' => PerangkatDaerahResource::make($perangkatDaerah),
    ], 201);
  }

  /**
   * Detail perangkat daerah (khusus admin).
   *
   * GET /api/perangkat-daerah/{perangkat_daerah}
   */
  public function show(PerangkatDaerah $perangkatDaerah): JsonResponse
  {
    $this->authorizeAdmin();

    $perangkatDaerah->load(['pegawai', 'kendaraan']);

    return response()->json([
      'data' => PerangkatDaerahResource::make($perangkatDaerah),
    ]);
  }

  /**
   * Perbarui perangkat daerah (khusus admin).
   *
   * PUT/PATCH /api/perangkat-daerah/{perangkat_daerah}
   */
  public function update(
    UpdatePerangkatDaerahRequest $request,
    PerangkatDaerah $perangkatDaerah
  ): JsonResponse {
    $perangkatDaerah->update($request->validated());

    return response()->json([
      'message' => 'Perangkat daerah berhasil diperbarui.',
      'data' => PerangkatDaerahResource::make($perangkatDaerah),
    ]);
  }

  /**
   * Hapus perangkat daerah (khusus admin).
   *
   * DELETE /api/perangkat-daerah/{perangkat_daerah}
   */
  public function destroy(PerangkatDaerah $perangkatDaerah): JsonResponse
  {
    $this->authorizeAdmin();

    if (
      $perangkatDaerah->pegawai()->exists() ||
      $perangkatDaerah->kendaraan()->exists()
    ) {
      return response()->json([
        'message' => 'Perangkat daerah masih memiliki data pegawai/kendaraan.',
      ], 409);
    }

    $perangkatDaerah->delete();

    return response()->json([
      'message' => 'Perangkat daerah berhasil dihapus.',
    ]);
  }

  /**
   * Hanya admin yang boleh mengelola master data.
   */
  private function authorizeAdmin(): void
  {
    abort_unless(
      $this->currentUser()->isAdmin(),
      403,
      'Hanya admin yang boleh mengakses data perangkat daerah.'
    );
  }
}
