<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('favicon.png') }}">
    <title>Dashboard Siswa | E-Ujian</title>

    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
        .navbar-light { background-color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-bottom: none; }
        .navbar-brand .brand-text { font-weight: 600 !important; color: #1e293b; }
        .content-wrapper { background-color: transparent; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); margin-bottom: 24px; transition: transform 0.2s; }
        /* .card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025); } */
        .card-header { background-color: #ffffff; border-bottom: 1px solid #f1f5f9; border-radius: 16px 16px 0 0 !important; padding: 1.25rem 1.5rem; }
        .card-title { font-weight: 600; color: #334155; }
        .btn { border-radius: 10px; font-weight: 500; padding: 0.5rem 1rem; letter-spacing: 0.025em; }
        .btn-primary { background-color: #3b82f6; border-color: #3b82f6; } /* Tailwind Blue 500 */
        .btn-primary:hover { background-color: #2563eb; border-color: #2563eb; }
        .nav-pills .nav-link.active, .nav-pills .show>.nav-link { background-color: #3b82f6; border-radius: 10px; }
        .main-footer { background: transparent; border: none; color: #64748b; font-size: 0.875rem; }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="{{ request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard') }}" class="navbar-brand">
        <img src="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('img/logo-placeholder.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ $globalInstitution->name ?? 'E-Ujian Siswa' }}</span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="{{ request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard') }}" class="nav-link">Dashboard</a>
          </li>
          <li class="nav-item">
            <a href="{{ request()->route('subdomain') ? route('institution.student.history.index', request()->route('subdomain')) : route('student.history.index') }}" class="nav-link">Riwayat Ujian</a>
          </li>
        </ul>
      </div>

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">{{ Auth::guard('student')->user()->name }}</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li>
                  <form action="{{ request()->route('subdomain') ? route('institution.student.logout', request()->route('subdomain')) : route('student.logout') }}" method="POST">
                      @csrf
                      <button type="submit" class="dropdown-item">Logout</button>
                  </form>
              </li>
            </ul>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"> @yield('page_title', 'Dashboard') </h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        @yield('content')
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Versi 1.0
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">{{ $globalInstitution->name ?? 'E-Ujian' }}</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
@stack('scripts')
</body>
</html>
