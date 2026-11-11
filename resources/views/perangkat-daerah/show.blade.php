@extends('layouts.app')

@section('content')
     <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
               <h4>Detail Perangkat Daerah</h4>
               <div class="d-flex gap-2">
                    <a href="{{ route('perangkat-daerah.edit', $perangkatDaerah->id) }}" class="btn btn-warning">
                         Edit
                    </a>
                    <a href="{{ route('perangkat-daerah.index') }}" class="btn btn-secondary">
                         Kembali
                    </a>
               </div>
          </div>

          <div class="card shadow-sm mb-3">
               <div class="card-header bg-primary text-white">
                    Informasi Perangkat Daerah: {{ $perangkatDaerah->nama_pd }}
               </div>
               <div class="card-body">
                    <div class="row">
                         @php
                              $fields = [
                                  'Kode' => $perangkatDaerah->kode_pd,
                                  'Nama' => $perangkatDaerah->nama_pd,
                                  'Kepala' => $perangkatDaerah->nama_kepala_pd,
                                  'No. Telepon' => $perangkatDaerah->no_telepon,
                                  'Alamat' => $perangkatDaerah->alamat,
                                  'Status' => $perangkatDaerah->status,
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

          <div class="row g-3">
               <div class="col-md-6">
                    <div class="card shadow-sm">
                         <div class="card-header bg-light">
                              Pegawai ({{ $perangkatDaerah->pegawai->count() }})
                         </div>
                         <div class="card-body p-0">
                              <div class="table-responsive">
                                   <table class="table table-bordered mb-0 align-middle">
                                        <thead class="table-light">
                                             <tr>
                                                  <th>Nama</th>
                                                  <th>NIP</th>
                                                  <th>Status</th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                             @forelse ($perangkatDaerah->pegawai as $item)
                                                  <tr>
                                                       <td>
                                                            <a href="{{ route('pegawai.show', $item->id) }}">
                                                                 {{ $item->nama }}
                                                            </a>
                                                       </td>
                                                       <td>{{ $item->nip }}</td>
                                                       <td>{{ $item->status }}</td>
                                                  </tr>
                                             @empty
                                                  <tr>
                                                       <td colspan="3" class="text-center text-muted py-4">
                                                            Belum ada pegawai.
                                                       </td>
                                                  </tr>
                                             @endforelse
                                        </tbody>
                                   </table>
                              </div>
                         </div>
                    </div>
               </div>

               <div class="col-md-6">
                    <div class="card shadow-sm">
                         <div class="card-header bg-light">
                              Kendaraan ({{ $perangkatDaerah->kendaraan->count() }})
                         </div>
                         <div class="card-body p-0">
                              <div class="table-responsive">
                                   <table class="table table-bordered mb-0 align-middle">
                                        <thead class="table-light">
                                             <tr>
                                                  <th>Nomor Polisi</th>
                                                  <th>Merk / Model</th>
                                                  <th>Kondisi</th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                             @forelse ($perangkatDaerah->kendaraan as $item)
                                                  <tr>
                                                       <td>
                                                            <a href="{{ route('kendaraan.show', $item->id) }}">
                                                                 {{ $item->nomor_polisi }}
                                                            </a>
                                                       </td>
                                                       <td>{{ $item->merk }} {{ $item->tipe_model }}</td>
                                                       <td>{{ $item->kondisi }}</td>
                                                  </tr>
                                             @empty
                                                  <tr>
                                                       <td colspan="3" class="text-center text-muted py-4">
                                                            Belum ada kendaraan.
                                                       </td>
                                                  </tr>
                                             @endforelse
                                        </tbody>
                                   </table>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
@endsection
