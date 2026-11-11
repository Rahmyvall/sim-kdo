<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">

     <title>Login | SIM-KDO</title>

     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

     <div class="container">
          <div class="row justify-content-center align-items-center" style="min-height:100vh">
               <div class="col-md-5 col-lg-4">

                    <div class="text-center mb-4">
                         <h3 class="fw-bold mb-1">
                              <i class="bi bi-car-front"></i> SIM-KDO
                         </h3>
                         <small class="text-muted">Sistem Informasi Manajemen Kendaraan Dinas Operasional</small>
                    </div>

                    <div class="card shadow-sm border-0">
                         <div class="card-body p-4">

                              @if ($errors->any())
                                   <div class="alert alert-danger">
                                        {{ $errors->first() }}
                                   </div>
                              @endif

                              <form action="{{ route('login.post') }}" method="POST">
                                   @csrf

                                   <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                             value="{{ old('email') }}" placeholder="nama@instansi.go.id" required
                                             autofocus>
                                   </div>

                                   <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control"
                                             placeholder="••••••••" required>
                                   </div>

                                   <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                             value="1" @checked(old('remember'))>
                                        <label class="form-check-label" for="remember">
                                             Ingat saya
                                        </label>
                                   </div>

                                   <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                                   </button>
                              </form>

                         </div>
                    </div>

               </div>
          </div>
     </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
