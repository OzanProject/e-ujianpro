<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin E-Ujian')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('favicon.png') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        @include('layouts.admin.navbar')
        @include('layouts.admin.sidebar')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page_title')</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            </div>
        @include('layouts.admin.footer')
    </div>
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: "{{ session('info') }}",
                });
            @endif
            
            @if(session('status'))
                let title = 'Info';
                let text = "{{ session('status') }}";
                let icon = 'info';

                // Customize for specific Breeze status keys
                if (text === 'profile-updated') {
                    title = 'Berhasil!';
                    text = 'Profil akun berhasil diperbarui.';
                    icon = 'success';
                } else if (text === 'password-updated') {
                    title = 'Berhasil!';
                    text = 'Password berhasil diubah.';
                    icon = 'success';
                } else if (text === 'verification-link-sent') {
                     title = ' Terkirim!';
                    text = 'Link verifikasi email baru telah dikirim.';
                    icon = 'success';
                }

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: text,
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
            
            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: "{{ session('warning') }}",
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>