@extends('layouts.app')

@section('content')
     <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
               <h4>Detail Pegawai</h4>
               <div class="d-flex gap-2">
                    <a href="{{ route('pegawai.edit', $pegawai->id) }}" class="btn btn-warning">
                         Edit
                    </a>
                    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">
                         Kembali
                    </a>
               </div>
          </div>

          <div class="card shadow-sm mb-3">
               <div class="card-header bg-primary text-white">
                    Informasi Pegawai: {{ $pegawai->nama }}
               </div>
               <div class="card-body">
                    <div class="row">
                         @php
                              $fields = [
                                  'Nama' => $pegawai->nama,
                                  'NIP' => $pegawai->nip,
                                  'Jabatan' => $pegawai->jabatan,
                                  'Golongan' => $pegawai->golongan,
                                  'Pangkat' => $pegawai->pangkat,
                                  'No. Telepon' => $pegawai->no_telepon,
                                  'Perangkat Daerah' => $pegawai->perangkatDaerah->nama_pd ?? '-',
                                  'Status' => $pegawai->status,
                              ];
                         @endphp

                         @foreach ($fields as $label => $value)
                              <div class="col-md-6 mb-3">
                                   <div class="text-muted small">{{ $label }}</div>
                                   <div class="fw-semibold">{{ $value ?: '-' }}</div>
                              </div>
                         @endforeach
                    </div>
               </div>
          </div>

          <div class="card shadow-sm">
               <div class="card-header bg-light">
                    Kendaraan yang Digunakan
               </div>
               <div class="card-body p-0">
                    <div class="table-responsive">
                         <table class="table table-bordered mb-0 align-middle">
                              <thead class="table-light">
                                   <tr>
                                        <th>Nomor Polisi</th>
                                        <th>Merk / Model</th>
                                        <th>Jenis</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @forelse ($pegawai->kendaraan as $item)
                                        <tr>
                                             <td>
                                                  <a href="{{ route('kendaraan.show', $item->id) }}">
                                                       {{ $item->nomor_polisi }}
                                                  </a>
                                             </td>
                                             <td>{{ $item->merk }} {{ $item->tipe_model }}</td>
                                             <td>{{ $item->jenis_kendaraan ?? '-' }}</td>
                                             <td>{{ $item->kondisi }}</td>
                                             <td>{{ $item->status_penggunaan }}</td>
                                        </tr>
                                   @empty
                                        <tr>
                                             <td colspan="5" class="text-center text-muted py-4">
                                                  Belum ada kendaraan untuk pegawai ini.
                                             </td>
                                        </tr>
                                   @endforelse
                              </tbody>
                         </table>
                    </div>
               </div>
          </div>
     </div>
@endsection
