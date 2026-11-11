@extends('layouts.app')

@section('content')
     <h3 class="fw-bold mb-4">
          Dashboard SIM-KDO
     </h3>

     <div class="row g-3">

          <div class="col-md-3">
               <div class="card shadow-sm border-0">
                    <div class="card-body">
                         <h6>Total Kendaraan</h6>
                         <h2>{{ $totalKendaraan }}</h2>
                    </div>
               </div>
          </div>

          <div class="col-md-3">
               <div class="card shadow-sm border-0">
                    <div class="card-body">
                         <h6>Kondisi Baik</h6>
                         <h2 class="text-success">{{ $baik }}</h2>
                    </div>
               </div>
          </div>

          <div class="col-md-3">
               <div class="card shadow-sm border-0">
                    <div class="card-body">
                         <h6>Rusak Ringan</h6>
                         <h2 class="text-warning">{{ $rusakRingan }}</h2>
                    </div>
               </div>
          </div>

          <div class="col-md-3">
               <div class="card shadow-sm border-0">
                    <div class="card-body">
                         <h6>Rusak Berat</h6>
                         <h2 class="text-danger">{{ $rusakBerat }}</h2>
                    </div>
               </div>
          </div>

     </div>
@endsection
