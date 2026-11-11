@extends('layouts.app')

@section('content')
     <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
               <div>
                    <h4 class="mb-1">Data Perangkat Daerah</h4>
                    <small class="text-muted">Master data perangkat daerah</small>
               </div>
               <a href="{{ route('perangkat-daerah.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Perangkat Daerah
               </a>
          </div>

          <div class="card shadow-sm">
               <div class="card-body">
                    <form action="{{ route('perangkat-daerah.index') }}" method="GET" class="row g-2 mb-3">
                         <div class="col-md-5">
                              <input type="text" name="search" class="form-control"
                                   placeholder="Cari kode atau nama perangkat daerah..." value="{{ request('search') }}">
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
                              <a href="{{ route('perangkat-daerah.index') }}" class="btn btn-secondary">
                                   Reset
                              </a>
                         </div>
                    </form>

                    <div class="table-responsive">
                         <table class="table table-bordered table-hover align-middle">
                              <thead class="table-light">
                                   <tr>
                                        <th width="5%">No.</th>
                                        <th>Kode</th>
                                        <th>Nama Perangkat Daerah</th>
                                        <th>Kepala</th>
                                        <th>No. Telepon</th>
                                        <th width="10%">Jumlah Pegawai</th>
                                        <th width="10%">Jumlah Kendaraan</th>
                                        <th>Status</th>
                                        <th width="150">Aksi</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @forelse($perangkatDaerah as $index => $item)
                                        <tr>
                                             <td>{{ $perangkatDaerah->firstItem() + $index }}</td>
                                             <td><strong>{{ $item->kode_pd }}</strong></td>
                                             <td>{{ $item->nama_pd }}</td>
                                             <td>{{ $item->nama_kepala_pd ?? '-' }}</td>
                                             <td>{{ $item->no_telepon ?? '-' }}</td>
                                             <td>{{ $item->pegawai_count }}</td>
                                             <td>{{ $item->kendaraan_count }}</td>
                                             <td>
                                                  @if ($item->status == 'Aktif')
                                                       <span class="badge bg-success">Aktif</span>
                                                  @else
                                                       <span class="badge bg-secondary">Tidak Aktif</span>
                                                  @endif
                                             </td>
                                             <td>
                                                  <div class="d-flex gap-1">
                                                       <a href="{{ route('perangkat-daerah.show', $item->id) }}"
                                                            class="btn btn-info btn-sm text-white" title="Detail">
                                                            <i class="bi bi-eye"></i>
                                                       </a>
                                                       <a href="{{ route('perangkat-daerah.edit', $item->id) }}"
                                                            class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                       </a>
                                                       <form action="{{ route('perangkat-daerah.destroy', $item->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Yakin ingin menghapus perangkat daerah ini?')">
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
                                                  Data perangkat daerah belum tersedia.
                                             </td>
                                        </tr>
                                   @endforelse
                              </tbody>
                         </table>
                    </div>

                    {{ $perangkatDaerah->links() }}
               </div>
          </div>
     </div>
@endsection
