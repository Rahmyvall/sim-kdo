@extends('layouts.app')

@section('content')
     <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
               <h4>Tambah Perangkat Daerah</h4>
               <a href="{{ route('perangkat-daerah.index') }}" class="btn btn-secondary">
                    Kembali
               </a>
          </div>

          <div class="card shadow-sm">
               <div class="card-body">
                    @if ($errors->any())
                         <div class="alert alert-danger">
                              <strong>Periksa kembali data yang diisi:</strong>
                              <ul class="mb-0 mt-2">
                                   @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                   @endforeach
                              </ul>
                         </div>
                    @endif

                    <form action="{{ route('perangkat-daerah.store') }}" method="POST">
                         @csrf
                         @include('perangkat-daerah._form')
                    </form>
               </div>
          </div>
     </div>
@endsection
