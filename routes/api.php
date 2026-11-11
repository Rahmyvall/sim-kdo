<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KendaraanController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\PerangkatDaerahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API SIM-KDO
|--------------------------------------------------------------------------
|
| Semua endpoint memakai prefix /api. Endpoint selain login & register
| dilindungi oleh token Bearer dari Laravel Sanctum.
|
| Contoh pemakaian di Postman:
|   Authorization: Bearer {token}
|
*/

/*
| Nama route diberi prefix "api." agar tidak bertabrakan dengan nama route
| web. Tanpa prefix ini, apiResource() menghasilkan nama seperti
| "kendaraan.index" yang sama dengan route di web.php sehingga
| redirect()->route('kendaraan.index') pada halaman web menjadi /api/kendaraan.
*/

Route::name('api.')->group(function () {

  Route::post('/login', [AuthController::class, 'login'])->name('login');

  Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('kendaraan', KendaraanController::class);

    Route::apiResource('pegawai', PegawaiController::class);

    Route::apiResource('perangkat-daerah', PerangkatDaerahController::class)
      ->parameters([
        'perangkat-daerah' => 'perangkat_daerah',
      ]);
  });
});
