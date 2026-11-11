@php
     $isEdit = isset($kendaraan);
@endphp

<div class="row">
     @if (auth()->user()->isAdmin())
          <div class="col-md-6 mb-3">
               <label class="form-label">Perangkat Daerah <span class="text-danger">*</span></label>
               <select name="perangkat_daerah_id" class="form-select" required>
                    <option value="">-- Pilih Perangkat Daerah --</option>
                    @foreach ($perangkatDaerah as $pd)
                         <option value="{{ $pd->id }}" @selected(old('perangkat_daerah_id', $kendaraan->perangkat_daerah_id ?? '') == $pd->id)>
                              {{ $pd->nama_pd }}
                         </option>
                    @endforeach
               </select>
          </div>
     @else
          <input type="hidden" name="perangkat_daerah_id" value="{{ auth()->user()->perangkat_daerah_id }}">
     @endif

     <div class="col-md-6 mb-3">
          <label class="form-label">Pegawai Pengguna</label>
          <select name="pegawai_id" class="form-select">
               <option value="">-- Belum Ditentukan --</option>
               @foreach ($pegawai as $p)
                    <option value="{{ $p->id }}" @selected(old('pegawai_id', $kendaraan->pegawai_id ?? '') == $p->id)>
                         {{ $p->nama }} ({{ $p->nip }})
                    </option>
               @endforeach
          </select>
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Nomor Polisi <span class="text-danger">*</span></label>
          <input type="text" name="nomor_polisi" class="form-control"
               value="{{ old('nomor_polisi', $kendaraan->nomor_polisi ?? '') }}" placeholder="Contoh: B 1234 ABC"
               required>
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Merk <span class="text-danger">*</span></label>
          <input type="text" name="merk" class="form-control" value="{{ old('merk', $kendaraan->merk ?? '') }}"
               required>
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Tipe / Model</label>
          <input type="text" name="tipe_model" class="form-control"
               value="{{ old('tipe_model', $kendaraan->tipe_model ?? '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Tahun Produksi</label>
          <input type="number" name="tahun_produksi" class="form-control"
               value="{{ old('tahun_produksi', $kendaraan->tahun_produksi ?? '') }}" min="1900"
               max="{{ date('Y') + 1 }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Warna</label>
          <input type="text" name="warna" class="form-control"
               value="{{ old('warna', $kendaraan->warna ?? '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
          <select name="jenis_kendaraan" class="form-select" required>
               <option value="">-- Pilih Jenis --</option>
               @foreach (['Mobil', 'Sepeda Motor', 'Truk', 'Bus', 'Kendaraan Khusus', 'Lainnya'] as $jenis)
                    <option value="{{ $jenis }}" @selected(old('jenis_kendaraan', $kendaraan->jenis_kendaraan ?? '') == $jenis)>
                         {{ $jenis }}
                    </option>
               @endforeach
          </select>
     </div>

     <div class="col-md-6 mb-3">
          <label class="form-label">Nomor Rangka</label>
          <input type="text" name="nomor_rangka" class="form-control"
               value="{{ old('nomor_rangka', $kendaraan->nomor_rangka ?? '') }}">
     </div>

     <div class="col-md-6 mb-3">
          <label class="form-label">Nomor Mesin</label>
          <input type="text" name="nomor_mesin" class="form-control"
               value="{{ old('nomor_mesin', $kendaraan->nomor_mesin ?? '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Bahan Bakar</label>
          <select name="bahan_bakar" class="form-select">
               <option value="">-- Pilih Bahan Bakar --</option>
               @foreach (['Bensin', 'Solar', 'Listrik', 'Hybrid', 'Lainnya'] as $bahan)
                    <option value="{{ $bahan }}" @selected(old('bahan_bakar', $kendaraan->bahan_bakar ?? '') == $bahan)>
                         {{ $bahan }}
                    </option>
               @endforeach
          </select>
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Kondisi <span class="text-danger">*</span></label>
          <select name="kondisi" class="form-select" required>
               <option value="">-- Pilih Kondisi --</option>
               @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kondisi)
                    <option value="{{ $kondisi }}" @selected(old('kondisi', $kendaraan->kondisi ?? '') == $kondisi)>
                         {{ $kondisi }}
                    </option>
               @endforeach
          </select>
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Status Penggunaan <span class="text-danger">*</span></label>
          <select name="status_penggunaan" class="form-select" required>
               <option value="">-- Pilih Status --</option>
               @foreach (['Digunakan', 'Tidak Digunakan', 'Dalam Perawatan', 'Dihapuskan'] as $status)
                    <option value="{{ $status }}" @selected(old('status_penggunaan', $kendaraan->status_penggunaan ?? '') == $status)>
                         {{ $status }}
                    </option>
               @endforeach
          </select>
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Nomor Register</label>
          <input type="text" name="nomor_register" class="form-control"
               value="{{ old('nomor_register', $kendaraan->nomor_register ?? '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Kode Barang</label>
          <input type="text" name="kode_barang" class="form-control"
               value="{{ old('kode_barang', $kendaraan->kode_barang ?? '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Tanggal Perolehan</label>
          <input type="date" name="tanggal_perolehan" class="form-control"
               value="{{ old('tanggal_perolehan', isset($kendaraan) && $kendaraan->tanggal_perolehan ? $kendaraan->tanggal_perolehan->format('Y-m-d') : '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Nilai Perolehan (Rp)</label>
          <input type="number" name="nilai_perolehan" class="form-control"
               value="{{ old('nilai_perolehan', isset($kendaraan) ? $kendaraan->nilai_perolehan : '') }}"
               min="0" step="0.01">
     </div>
</div>

<div class="d-flex gap-2 mt-3">
     <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> {{ $isEdit ? 'Perbarui Data' : 'Simpan Data' }}
     </button>
     <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">
          Batal
     </a>
</div>
