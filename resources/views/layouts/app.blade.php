<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">

     <title>SIM-KDO | Dashboard</title>

     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

     <div class="d-flex">

          <aside class="bg-primary text-white p-3" style="width:250px; min-height:100vh">

               <h4 class="mb-4">
                    <i class="bi bi-car-front"></i> SIM-KDO
               </h4>

               <a href="/" class="text-white d-block p-2 text-decoration-none">
                    <i class="bi bi-grid"></i> Dashboard
               </a>

               <a href="/kendaraan" class="text-white d-block p-2 text-decoration-none">
                    <i class="bi bi-car-front"></i> Data Kendaraan
               </a>

               <a href="/pegawai" class="text-white d-block p-2 text-decoration-none">
                    <i class="bi bi-people"></i> Data Pegawai
               </a>

               @if (auth()->user()->isAdmin())
                    <a href="/perangkat-daerah" class="text-white d-block p-2 text-decoration-none">
                         <i class="bi bi-building"></i> Perangkat Daerah
                    </a>
               @endif

               <form action="{{ route('logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button class="btn btn-light btn-sm w-100">
                         Logout
                    </button>
               </form>

          </aside>

          <main class="flex-grow-1 p-4">

               @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                         {{ session('error') }}
                         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
               @endif

               @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                         {{ session('success') }}
                         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
               @endif

               @yield('content')

          </main>

     </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
