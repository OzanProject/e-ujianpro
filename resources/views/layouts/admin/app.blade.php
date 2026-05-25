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
                    timer: 3000,
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#003778',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: "{{ session('info') }}",
                    confirmButtonColor: '#003778',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            @endif
            
            @if(session('status'))
                @php
                    $statusText = session('status');
                    $title = 'Info';
                    $icon = 'info';
                    
                    if ($statusText === 'profile-updated') {
                        $title = 'Berhasil!';
                        $statusText = 'Profil akun berhasil diperbarui.';
                        $icon = 'success';
                    } elseif ($statusText === 'password-updated') {
                        $title = 'Berhasil!';
                        $statusText = 'Password berhasil diubah.';
                        $icon = 'success';
                    } elseif ($statusText === 'verification-link-sent') {
                        $title = 'Terkirim!';
                        $statusText = 'Link verifikasi email baru telah dikirim.';
                        $icon = 'success';
                    }
                @endphp
                Swal.fire({
                    icon: '{{ $icon }}',
                    title: '{{ $title }}',
                    text: '{{ $statusText }}',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            @endif
            
            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: "{{ session('warning') }}",
                    confirmButtonColor: '#003778',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            @endif

            // Script untuk Popup Titi Mangsa (Tanggal Cetak)
            $(document).on('click', '.btn-print-with-date', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                
                // Jika tombol ini berada dalam form (bukan link)
                if (!href) {
                    var form = $(this).closest('form');
                    if (form.length) {
                        Swal.fire({
                            title: 'Titi Mangsa (Tanggal Cetak)',
                            text: 'Pilih tanggal yang akan tertera pada dokumen:',
                            input: 'date',
                            inputValue: new Date().toISOString().split('T')[0],
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-print"></i> Lanjutkan Cetak',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'print_date',
                                    value: result.value
                                }).appendTo(form);
                                form.submit();
                            }
                        });
                        return;
                    }
                }

                // Jika tombol adalah link anchor biasa
                Swal.fire({
                    title: 'Titi Mangsa (Tanggal Cetak)',
                    text: 'Pilih tanggal yang akan tertera pada dokumen:',
                    input: 'date',
                    inputValue: new Date().toISOString().split('T')[0],
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print"></i> Lanjutkan Cetak',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        var separator = href.indexOf('?') !== -1 ? '&' : '?';
                        window.open(href + separator + 'print_date=' + result.value, '_blank');
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>