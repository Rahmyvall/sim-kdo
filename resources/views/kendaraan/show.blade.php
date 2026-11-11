@extends('layouts.app')

@section('content')
     <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
               <h4>Detail Kendaraan</h4>
               <div class="d-flex gap-2">
                    <a href="{{ route('kendaraan.edit', $kendaraan->id) }}" class="btn btn-warning">
                         Edit
                    </a>
                    <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">
                         Kembali
                    </a>
               </div>
          </div>

          <div class="card shadow-sm">
               <div class="card-header bg-primary text-white">
                    Informasi Kendaraan: {{ $kendaraan->nomor_polisi }}
               </div>
               <div class="card-body">
                    <div class="row">
                         @php
                              $fields = [
                                  'Nomor Polisi' => $kendaraan->nomor_polisi,
                                  'Perangkat Daerah' => $kendaraan->perangkatDaerah->nama_pd ?? '-',
                                  'Pegawai Pengguna' => $kendaraan->pegawai->nama ?? '-',
                                  'Merk' => $kendaraan->merk,
                                  'Tipe / Model' => $kendaraan->tipe_model,
                                  'Tahun Produksi' => $kendaraan->tahun_produksi,
                                  'Warna' => $kendaraan->warna,
                                  'Jenis Kendaraan' => $kendaraan->jenis_kendaraan,
                                  'Nomor Rangka' => $kendaraan->nomor_rangka,
                                  'Nomor Mesin' => $kendaraan->nomor_mesin,
                                  'Bahan Bakar' => $kendaraan->bahan_bakar,
                                  'Kondisi' => $kendaraan->kondisi,
                                  'Status Penggunaan' => $kendaraan->status_penggunaan,
                                  'Nomor Register' => $kendaraan->nomor_register,
                                  'Kode Barang' => $kendaraan->kode_barang,
                                  'Tanggal Perolehan' => optional($kendaraan->tanggal_perolehan)->format('d-m-Y'),
                                  'Nilai Perolehan' =>
                                      $kendaraan->nilai_perolehan !== null
                                          ? 'Rp ' . number_format((float) $kendaraan->nilai_perolehan, 0, ',', '.')
                                          : '-',
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
     </div>
@endsection
