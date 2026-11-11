@extends('layouts.app')

@section('content')
     <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
               <div>
                    <h4 class="mb-1">Data Pegawai</h4>
                    <small class="text-muted">Manajemen pegawai pengguna kendaraan dinas</small>
               </div>
               <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Pegawai
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
                    <form action="{{ route('pegawai.index') }}" method="GET" class="row g-2 mb-3">
                         <div class="col-md-5">
                              <input type="text" name="search" class="form-control"
                                   placeholder="Cari nama, NIP, atau jabatan..." value="{{ request('search') }}">
                         </div>
                         <div class="col-md-3">
                              <select name="status" class="form-select">
                                   <option value="">Semua Status</option>
                                   @foreach (['Aktif', 'Tidak Aktif'] as $status)
                                        <option value="{{ $status }}" @selected(request('status') == $status)>
                                             {{ $status }}
                                        </option>
                                   @endforeach
                              </select>
                         </div>
                         <div class="col-md-4 d-flex gap-2">
                              <button class="btn btn-primary" type="submit">
                                   <i class="bi bi-search"></i> Cari
                              </button>
                              <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">
                                   Reset
                              </a>
                         </div>
                    </form>

                    <div class="table-responsive">
                         <table class="table table-bordered table-hover align-middle">
                              <thead class="table-light">
                                   <tr>
                                        <th width="5%">No.</th>
                                        <th>Nama</th>
                                        <th>NIP</th>
                                        <th>Jabatan</th>
                                        <th>Perangkat Daerah</th>
                                        <th>No. Telepon</th>
                                        <th>Status</th>
                                        <th width="150">Aksi</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @forelse($pegawai as $index => $item)
                                        <tr>
                                             <td>{{ $pegawai->firstItem() + $index }}</td>
                                             <td><strong>{{ $item->nama }}</strong></td>
                                             <td>{{ $item->nip }}</td>
                                             <td>{{ $item->jabatan ?? '-' }}</td>
                                             <td>{{ $item->perangkatDaerah->nama_pd ?? '-' }}</td>
                                             <td>{{ $item->no_telepon ?? '-' }}</td>
                                             <td>
                                                  @if ($item->status == 'Aktif')
                                                       <span class="badge bg-success">Aktif</span>
                                                  @else
                                                       <span class="badge bg-secondary">Tidak Aktif</span>
                                                  @endif
                                             </td>
                                             <td>
                                                  <div class="d-flex gap-1">
                                                       <a href="{{ route('pegawai.show', $item->id) }}"
                                                            class="btn btn-info btn-sm text-white" title="Detail">
                                                            <i class="bi bi-eye"></i>
                                                       </a>
                                                       <a href="{{ route('pegawai.edit', $item->id) }}"
                                                            class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                       </a>
                                                       <form action="{{ route('pegawai.destroy', $item->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
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
                                             <td colspan="8" class="text-center text-muted py-4">
                                                  Data pegawai belum tersedia.
                                             </td>
                                        </tr>
                                   @endforelse
                              </tbody>
                         </table>
                    </div>

                    {{ $pegawai->links() }}
               </div>
          </div>
     </div>
@endsection
