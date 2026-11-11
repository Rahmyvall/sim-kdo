<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
  /**
   * Batasi akses ke data sesuai kepemilikan perangkat daerah.
   *
   * Admin dapat mengakses seluruh data, sedangkan operator hanya boleh
   * mengakses data milik perangkat daerahnya sendiri.
   */
  protected function authorizeOwnership(Model $model): void
  {
    $user = $this->currentUser();

    if ($user->isAdmin()) {
      return;
    }

    abort_unless(
      $model->getAttribute('perangkat_daerah_id') == $user->perangkat_daerah_id,
      403,
      'Anda tidak memiliki akses ke data ini.'
    );
  }

  /**
   * Paksa perangkat_daerah_id sesuai milik user untuk operator.
   *
   * @param  array<string, mixed>  $data
   * @return array<string, mixed>
   */
  protected function applyOwnershipScope(array $data): array
  {
    $user = $this->currentUser();

    if (! $user->isAdmin()) {
      $data['perangkat_daerah_id'] = $user->perangkat_daerah_id;
    }

    return $data;
  }

  /**
   * Jumlah data per halaman pada endpoint daftar.
   */
  protected function perPage(Request $request): int
  {
    $perPage = (int) $request->integer('per_page', 10);

    return min(100, max(1, $perPage === 0 ? 10 : $perPage));
  }
}
