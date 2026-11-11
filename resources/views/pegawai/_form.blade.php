@php
     $isEdit = isset($pegawai);
@endphp

<div class="row">
     @if (auth()->user()->isAdmin())
          <div class="col-md-6 mb-3">
               <label class="form-label">Perangkat Daerah <span class="text-danger">*</span></label>
               <select name="perangkat_daerah_id" class="form-select" required>
                    <option value="">-- Pilih Perangkat Daerah --</option>
                    @foreach ($perangkatDaerah as $pd)
                         <option value="{{ $pd->id }}" @selected(old('perangkat_daerah_id', $pegawai->perangkat_daerah_id ?? '') == $pd->id)>
                              {{ $pd->nama_pd }}
                         </option>
                    @endforeach
               </select>
          </div>
     @else
          <input type="hidden" name="perangkat_daerah_id" value="{{ auth()->user()->perangkat_daerah_id }}">
     @endif

     <div class="col-md-6 mb-3">
          <label class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
          <input type="text" name="nama" class="form-control" value="{{ old('nama', $pegawai->nama ?? '') }}"
               placeholder="Nama lengkap" required>
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">NIP <span class="text-danger">*</span></label>
          <input type="text" name="nip" class="form-control" value="{{ old('nip', $pegawai->nip ?? '') }}"
               required>
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Jabatan</label>
          <input type="text" name="jabatan" class="form-control"
               value="{{ old('jabatan', $pegawai->jabatan ?? '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">No. Telepon</label>
          <input type="text" name="no_telepon" class="form-control"
               value="{{ old('no_telepon', $pegawai->no_telepon ?? '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Golongan</label>
          <input type="text" name="golongan" class="form-control"
               value="{{ old('golongan', $pegawai->golongan ?? '') }}" placeholder="Contoh: III/a">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Pangkat</label>
          <input type="text" name="pangkat" class="form-control"
               value="{{ old('pangkat', $pegawai->pangkat ?? '') }}">
     </div>

     <div class="col-md-4 mb-3">
          <label class="form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-select" required>
               @foreach (['Aktif', 'Tidak Aktif'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $pegawai->status ?? 'Aktif') == $status)>
                         {{ $status }}
                    </option>
               @endforeach
          </select>
     </div>
</div>

<div class="d-flex gap-2 mt-3">
     <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> {{ $isEdit ? 'Perbarui Data' : 'Simpan Data' }}
     </button>
     <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">
          Batal
     </a>
</div>
