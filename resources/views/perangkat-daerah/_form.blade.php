@php
     $isEdit = isset($perangkatDaerah);
@endphp

<div class="row">
     <div class="col-md-4 mb-3">
          <label class="form-label">Kode Perangkat Daerah <span class="text-danger">*</span></label>
          <input type="text" name="kode_pd" class="form-control"
               value="{{ old('kode_pd', $perangkatDaerah->kode_pd ?? '') }}" placeholder="Contoh: 1.01.01" required>
     </div>

     <div class="col-md-8 mb-3">
          <label class="form-label">Nama Perangkat Daerah <span class="text-danger">*</span></label>
          <input type="text" name="nama_pd" class="form-control"
               value="{{ old('nama_pd', $perangkatDaerah->nama_pd ?? '') }}" required>
     </div>

     <div class="col-md-6 mb-3">
          <label class="form-label">Nama Kepala Perangkat Daerah</label>
          <input type="text" name="nama_kepala_pd" class="form-control"
               value="{{ old('nama_kepala_pd', $perangkatDaerah->nama_kepala_pd ?? '') }}">
     </div>

     <div class="col-md-3 mb-3">
          <label class="form-label">No. Telepon</label>
          <input type="text" name="no_telepon" class="form-control"
               value="{{ old('no_telepon', $perangkatDaerah->no_telepon ?? '') }}">
     </div>

     <div class="col-md-3 mb-3">
          <label class="form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-select" required>
               @foreach (['Aktif', 'Tidak Aktif'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $perangkatDaerah->status ?? 'Aktif') == $status)>
                         {{ $status }}
                    </option>
               @endforeach
          </select>
     </div>

     <div class="col-12 mb-3">
          <label class="form-label">Alamat</label>
          <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $perangkatDaerah->alamat ?? '') }}</textarea>
     </div>
</div>

<div class="d-flex gap-2 mt-3">
     <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> {{ $isEdit ? 'Perbarui Data' : 'Simpan Data' }}
     </button>
     <a href="{{ route('perangkat-daerah.index') }}" class="btn btn-secondary">
          Batal
     </a>
</div>
