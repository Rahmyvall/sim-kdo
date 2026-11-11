@extends('layouts.app')

@section('content')
     <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
               <div>
                    <h4 class="mb-1">Data Kendaraan</h4>
                    <small class="text-muted">Manajemen kendaraan dinas operasional</small>
               </div>
               <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kendaraan
               </a>
          </div>

          @if (session('success'))
               <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
               </div>
          @endif

          <div class="card shadow-sm">
               <div class="card-body">
                    <form action="{{ route('kendaraan.index') }}" method="GET" class="row g-2 mb-3">
                         <div class="col-md-5">
                              <input type="text" name="search" class="form-control"
                                   placeholder="Cari nomor polisi, merk, tipe..." value="{{ request('search') }}">
                         </div>
                         <div class="col-md-3">
                              <select name="kondisi" class="form-select">
                                   <option value="">Semua Kondisi</option>
                                   @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kondisi)
                                        <option value="{{ $kondisi }}" @selected(request('kondisi') == $kondisi)>
                                             {{ $kondisi }}
                                        </option>
                                   @endforeach
                              </select>
                         </div>
                         <div class="col-md-4 d-flex gap-2">
                              <button class="btn btn-primary" type="submit">
                                   <i class="bi bi-search"></i> Cari
                              </button>
                              <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">
                                   Reset
                              </a>
                         </div>
                    </form>

                    <div class="table-responsive">
                         <table class="table table-bordered table-hover align-middle">
                              <thead class="table-light">
                                   <tr>
                                        <th width="5%">No.</th>
                                        <th>Nomor Polisi</th>
                                        <th>Merk / Model</th>
                                        <th>Jenis</th>
                                        <th>Perangkat Daerah</th>
                                        <th>Pegawai</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
                                        <th width="150">Aksi</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @forelse($kendaraan as $index => $item)
                                        <tr>
                                             <td>{{ $kendaraan->firstItem() + $index }}</td>
                                             <td><strong>{{ $item->nomor_polisi }}</strong></td>
                                             <td>{{ $item->merk }} {{ $item->tipe_model }}</td>
                                             <td>{{ $item->jenis_kendaraan }}</td>
                                             <td>{{ $item->perangkatDaerah->nama_pd ?? '-' }}</td>
                                             <td>{{ $item->pegawai->nama ?? '-' }}</td>
                                             <td>
                                                  @if ($item->kondisi == 'Baik')
                                                       <span class="badge bg-success">Baik</span>
                                                  @elseif($item->kondisi == 'Rusak Ringan')
                                                       <span class="badge bg-warning text-dark">Rusak Ringan</span>
                                                  @else
                                                       <span class="badge bg-danger">Rusak Berat</span>
                                                  @endif
                                             </td>
                                             <td>{{ $item->status_penggunaan }}</td>
                                             <td>
                                                  <div class="d-flex gap-1">
                                                       <a href="{{ route('kendaraan.show', $item->id) }}"
                                                            class="btn btn-info btn-sm text-white" title="Detail">
                                                            <i class="bi bi-eye"></i>
                                                       </a>
                                                       <a href="{{ route('kendaraan.edit', $item->id) }}"
                                                            class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                       </a>
                                                       <form action="{{ route('kendaraan.destroy', $item->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                 title="Hapus">
                                                                 <i class="bi bi-trash"></i>
                                                            </button>
                                                       </form>
                                                  </div>
                                             </td>
                                        </tr>
                                   @empty
                                        <tr>
                                             <td colspan="9" class="text-center text-muted py-4">
                                                  Data kendaraan belum tersedia.
                                             </td>
                                        </tr>
                                   @endforelse
                              </tbody>
                         </table>
                    </div>

                    {{ $kendaraan->links() }}
               </div>
          </div>
     </div>
@endsection
