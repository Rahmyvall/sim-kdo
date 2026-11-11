# SIM-KDO REST API

Dokumentasi REST API SIM-KDO (Laravel 13 + Laravel Sanctum).

Base URL: `http://127.0.0.1:8000/api`

---

## 1. Menjalankan

```sh
php artisan migrate --seed
php artisan serve
```

Akun demo hasil seeder:

| Email | Password | Role |
| --- | --- | --- |
| `admin@sim-kdo.test` | `password` | admin (akses semua data) |
| `operator@sim-kdo.test` | `password` | operator (hanya data perangkat daerahnya) |

---

## 2. Autentikasi

API memakai **Bearer Token** dari Sanctum.

1. Kirim `POST /api/login` untuk mendapatkan token.
2. Sertakan token pada setiap request:

```http
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxx
Accept: application/json
```

> Selalu kirim header `Accept: application/json` agar error (401/403/422/404) dibalas dalam bentuk JSON.

---

## 3. Format Respons

**Sukses (satu data)**

```json
{
  "message": "Data kendaraan berhasil ditambahkan.",
  "data": { "id": 1, "nomor_polisi": "B 1234 ABC" }
}
```

**Sukses (daftar ber-paginasi)**

```json
{
  "data": [ { "id": 1 } ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "per_page": 10, "total": 1, "last_page": 1 }
}
```

**Error**

```json
{ "message": "Unauthenticated." }
```

```json
{
  "message": "Perangkat daerah wajib dipilih. (and 4 more errors)",
  "errors": {
    "perangkat_daerah_id": ["Perangkat daerah wajib dipilih."],
    "nomor_polisi": ["Nomor polisi wajib diisi."]
  }
}
```

| Kode | Arti |
| --- | --- |
| `200` | Berhasil |
| `201` | Berhasil membuat data baru |
| `401` | Token tidak ada / sudah tidak valid (mis. setelah logout) |
| `403` | Tidak punya hak akses (data milik perangkat daerah lain, atau bukan admin) |
| `404` | Data tidak ditemukan |
| `409` | Konflik (perangkat daerah masih punya pegawai/kendaraan) |
| `422` | Validasi gagal |

---

## 4. Endpoint

### 4.1 Auth

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| `POST` | `/api/login` | Login, dapat Bearer token |
| `GET` | `/api/me` | Profil user yang sedang login |
| `POST` | `/api/logout` | Menghapus token yang sedang dipakai |

**POST `/api/login`**

```json
{
  "email": "admin@sim-kdo.test",
  "password": "password",
  "device_name": "postman"
}
```

Respons `200`:

```json
{
  "message": "Login berhasil.",
  "data": {
    "token": "1|no7vWKJxwS8Ojy...",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@sim-kdo.test",
      "role": "admin",
      "perangkat_daerah_id": null
    }
  }
}
```

`device_name` opsional (default `postman`) — dipakai sebagai label token di tabel `personal_access_tokens`.

**GET `/api/me`** → `data` berisi data user + relasi `perangkat_daerah`.

**POST `/api/logout`** → token yang dipakai dihapus; request berikutnya dengan token itu mendapat `401`.

---

### 4.2 Kendaraan

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| `GET` | `/api/kendaraan` | Daftar kendaraan |
| `POST` | `/api/kendaraan` | Tambah kendaraan |
| `GET` | `/api/kendaraan/{id}` | Detail kendaraan |
| `PUT/PATCH` | `/api/kendaraan/{id}` | Ubah kendaraan |
| `DELETE` | `/api/kendaraan/{id}` | Hapus kendaraan (soft delete) |

**Query parameter daftar:** `search`, `kondisi`, `status_penggunaan`, `perangkat_daerah_id`, `per_page` (1–100, default 10), `page`.

**Body tambah/ubah:**

| Field | Wajib | Aturan |
| --- | --- | --- |
| `nomor_polisi` | Ya | maks 20, unik |
| `merk` | Ya | maks 100 |
| `jenis_kendaraan` | Ya | maks 60 |
| `kondisi` | Ya | `Baik`, `Rusak Ringan`, `Rusak Berat` |
| `status_penggunaan` | Ya | `Digunakan`, `Tidak Digunakan`, `Dalam Perawatan`, `Dihapuskan` |
| `perangkat_daerah_id` | Ya (admin) | harus ada di tabel `perangkat_daerah` |
| `pegawai_id` | Tidak | harus ada di tabel `pegawai` |
| `tipe_model`, `warna`, `bahan_bakar`, `nomor_rangka`, `nomor_mesin`, `nomor_register`, `kode_barang` | Tidak | maks 40–100 |
| `tahun_produksi` | Tidak | 1900–2100 |
| `tanggal_perolehan` | Tidak | format `Y-m-d` |
| `nilai_perolehan` | Tidak | numeric ≥ 0 |

```json
{
  "perangkat_daerah_id": 1,
  "pegawai_id": null,
  "nomor_polisi": "B 1234 ABC",
  "merk": "Toyota",
  "tipe_model": "Avanza",
  "tahun_produksi": 2020,
  "warna": "Hitam",
  "jenis_kendaraan": "Mobil",
  "bahan_bakar": "Bensin",
  "kondisi": "Baik",
  "status_penggunaan": "Digunakan",
  "tanggal_perolehan": "2020-01-15",
  "nilai_perolehan": 250000000
}
```

---

### 4.3 Pegawai

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| `GET` | `/api/pegawai` | Daftar pegawai |
| `POST` | `/api/pegawai` | Tambah pegawai |
| `GET` | `/api/pegawai/{id}` | Detail pegawai + daftar kendaraannya |
| `PUT/PATCH` | `/api/pegawai/{id}` | Ubah pegawai |
| `DELETE` | `/api/pegawai/{id}` | Hapus pegawai (soft delete) |

**Query parameter daftar:** `search`, `status`, `perangkat_daerah_id`, `per_page`, `page`.

**Body:**

| Field | Wajib | Aturan |
| --- | --- | --- |
| `nama` | Ya | maks 100 |
| `nip` | Ya | maks 30, unik |
| `status` | Ya | `Aktif`, `Tidak Aktif` |
| `perangkat_daerah_id` | Ya (admin) | harus ada di tabel `perangkat_daerah` |
| `jabatan`, `golongan`, `pangkat`, `no_telepon` | Tidak | maks 20–100 |

```json
{
  "perangkat_daerah_id": 1,
  "nama": "Budi Santoso",
  "nip": "198701012010011001",
  "jabatan": "Kepala Seksi",
  "golongan": "III/c",
  "pangkat": "Penata",
  "no_telepon": "081234567890",
  "status": "Aktif"
}
```

---

### 4.4 Perangkat Daerah (khusus admin)

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| `GET` | `/api/perangkat-daerah` | Daftar perangkat daerah |
| `POST` | `/api/perangkat-daerah` | Tambah perangkat daerah |
| `GET` | `/api/perangkat-daerah/{id}` | Detail + seluruh pegawai & kendaraan |
| `PUT/PATCH` | `/api/perangkat-daerah/{id}` | Ubah perangkat daerah |
| `DELETE` | `/api/perangkat-daerah/{id}` | Hapus perangkat daerah |

> Operator mendapat `403`. Parameter path memakai underscore: `{perangkat_daerah}`.

**Query parameter daftar:** `search`, `status`, `per_page`, `page`.

**Body:**

| Field | Wajib | Aturan |
| --- | --- | --- |
| `kode_pd` | Ya | maks 30, unik |
| `nama_pd` | Ya | maks 150 |
| `status` | Ya | `Aktif`, `Tidak Aktif` |
| `alamat`, `nama_kepala_pd`, `no_telepon` | Tidak | maks 20–255 |

```json
{
  "kode_pd": "1.01.02",
  "nama_pd": "Dinas Kesehatan",
  "alamat": "Jl. Ahmad Yani No. 1",
  "nama_kepala_pd": "Kepala Dinas Kesehatan",
  "no_telepon": "021-7654321",
  "status": "Aktif"
}
```

`DELETE` mengembalikan `409` bila perangkat daerah masih memiliki pegawai atau kendaraan.

---

## 5. Aturan Hak Akses

| Role | Kendaraan | Pegawai | Perangkat Daerah |
| --- | --- | --- | --- |
| **admin** | Semua data | Semua data | Penuh (CRUD) |
| **operator** | Hanya `perangkat_daerah_id` miliknya | Hanya miliknya | `403` |

Untuk operator, field `perangkat_daerah_id` yang dikirim akan **diabaikan dan dipaksa** memakai perangkat daerah milik user. Mengakses data milik perangkat daerah lain menghasilkan `403`.

---

## 6. Contoh Cepat (curl)

```sh
# Login
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"email":"admin@sim-kdo.test","password":"password","device_name":"curl"}' \
  | php -r '$d=json_decode(stream_get_contents(STDIN),true); echo $d["data"]["token"];')

# Daftar kendaraan
curl -s http://127.0.0.1:8000/api/kendaraan \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json"

# Logout
curl -s -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json"
```

---

## 7. Postman

Import `docs/postman/SIM-KDO.postman_collection.json` ke Postman.

1. Pastikan variabel koleksi `base_url` = `http://127.0.0.1:8000/api`.
2. Jalankan request **Auth > Login**. Script test otomatis menyimpan token ke variabel `{{token}}`.
3. Request lain sudah memakai `Authorization: Bearer {{token}}` dari level koleksi.
4. Request **Tambah Kendaraan / Pegawai / Perangkat Daerah** otomatis menyimpan `id` hasilnya ke variabel `{{kendaraan_id}}` / `{{pegawai_id}}` / `{{perangkat_daerah_id}}` sehingga request Detail/Ubah/Hapus bisa langsung dijalankan.

---

## 8. Struktur Kode

```
routes/api.php                                  # semua route API (prefix nama "api.")
app/Http/Controllers/Api/
  ApiController.php                             # helper scope kepemilikan & paginasi
  AuthController.php                            # login, me, logout
  KendaraanController.php
  PegawaiController.php
  PerangkatDaerahController.php
app/Http/Requests/Api/                          # validasi (Store*/Update*, LoginRequest)
app/Http/Resources/                             # bentuk JSON (Kendaraan/Pegawai/PerangkatDaerah)
tests/Feature/Api/                              # 30+ test API
```

Nama route API diberi prefix `api.` (mis. `api.kendaraan.index`) supaya tidak bertabrakan dengan nama route web (`kendaraan.index`) yang dipakai `redirect()->route(...)` di halaman Blade.

---

## 9. Menjalankan Test

```sh
php artisan test
```

Seluruh test API ada di `tests/Feature/Api/` dan mencakup autentikasi, validasi, paginasi, filter, hak akses admin/operator, serta soft delete.
