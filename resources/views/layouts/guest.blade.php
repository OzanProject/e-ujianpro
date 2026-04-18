<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $isTenant = isset($globalInstitution) && $globalInstitution;
        $appName = $isTenant ? $globalInstitution->name : \App\Models\Setting::getValue('app_name', 'E-Ujian PRO');
        
        if ($isTenant && $globalInstitution->logo) {
            $logoPath = \Illuminate\Support\Str::startsWith($globalInstitution->logo, ['http://', 'https://']) ? $globalInstitution->logo : asset('storage/' . $globalInstitution->logo);
        } else {
            $rawLogo = \App\Models\Setting::getValue('app_logo', 'img/logo-placeholder.png');
            if (\Illuminate\Support\Str::startsWith($rawLogo, ['http://', 'https://'])) {
                $logoPath = $rawLogo;
            } elseif (\Illuminate\Support\Str::startsWith($rawLogo, 'img/')) {
                $logoPath = asset($rawLogo);
            } else {
                $logoPath = asset('storage/' . $rawLogo);
            }
        }
    @endphp

    <title>@yield('title', $appName)</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $logoPath }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#003778",
                        "primary-container": "#0a4da1",
                        "on-primary": "#ffffff",
                        "secondary": "#525f73",
                        "surface": "#f8f9fa",
                        "on-surface": "#191c1d",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-highest": "#e1e3e4",
                        "outline-variant": "#c3c6d3",
                        "on-surface-variant": "#424752",
                    },
                    fontFamily: {
                        sans: ['Public Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer utilities {
            .glass-panel {
                @apply bg-white/80 backdrop-blur-[20px] shadow-[0_8px_40px_rgba(25,28,29,0.06)];
            }
            .primary-gradient {
                @apply bg-gradient-to-br from-primary to-primary-container text-on-primary;
            }
            .input-edu {
                @apply block w-full pl-10 pr-3 py-3 bg-[#e1e3e4]/30 border border-transparent rounded-xl text-base transition-all duration-200 focus:bg-white focus:border-[#003778] focus:ring-1 focus:ring-[#003778] focus:outline-none;
            }
        }
        body {
            @apply font-sans bg-surface text-on-surface antialiased flex flex-col min-h-screen;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col antialiased">

    <header class="bg-[#f8f9fa] dark:bg-slate-950 w-full top-0 z-50">
        <div class="flex justify-between items-center w-full px-4 sm:px-8 py-4 max-w-7xl mx-auto">
            <a href="{{ url('/') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                <img src="{{ $logoPath }}" alt="Logo" class="h-8 w-auto">
                <span class="text-xl sm:text-2xl font-bold tracking-tight text-[#0A4DA1] dark:text-blue-400">
                    {{ $appName }}
                </span>
            </a>
            <div class="flex items-center gap-4 sm:gap-6">
                @php $routeName = Route::currentRouteName(); @endphp
                @if($routeName == 'login' || $routeName == 'student.login')
                    @if(Route::has('register.sekolah'))
                        <a class="text-slate-600 font-medium hover:text-[#003778] transition-colors text-sm" href="{{ route('register.sekolah') }}">Daftar</a>
                    @endif
                    <span class="text-[#0A4DA1] font-bold border-b-2 border-[#0A4DA1] pb-1 text-sm">Login</span>
                @elseif($routeName == 'register.sekolah')
                    <span class="text-[#0A4DA1] font-bold border-b-2 border-[#0A4DA1] pb-1 text-sm">Daftar</span>
                    <a class="text-slate-600 font-medium hover:text-[#003778] transition-colors text-sm" href="{{ route('login') }}">Login</a>
                @endif
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-surface">
        {{-- Background decorative elements --}}
        <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[50%] bg-[#d6e3fb]/30 rounded-[100%] blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[50%] h-[60%] bg-[#0a4da1]/10 rounded-[100%] blur-[120px] pointer-events-none"></div>

        @yield('main_content')
        
    </main>

    <footer class="bg-[#e7e8e9] dark:bg-slate-900 w-full py-8 sm:py-12 mt-auto border-t-0">
        <div class="flex flex-col md:flex-row justify-between items-center px-4 sm:px-8 w-full max-w-7xl mx-auto space-y-4 md:space-y-0 text-center md:text-left">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-500 text-xl">school</span>
                <span class="text-lg font-bold text-slate-700 dark:text-slate-300">{{ $appName }}</span>
            </div>
            <nav class="flex flex-wrap justify-center gap-4 sm:gap-6">
                <a class="text-sm font-medium text-slate-500 hover:text-[#0A4DA1] transition-colors" href="#">Help Center</a>
                <a class="text-sm font-medium text-slate-500 hover:text-[#0A4DA1] transition-colors" href="#">Privacy Policy</a>
                <a class="text-sm font-medium text-slate-500 hover:text-[#0A4DA1] transition-colors" href="#">Terms of Service</a>
            </nav>
            <div class="text-sm text-slate-500">
                © {{ date('Y') }} {{ $appName }} Systems
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                    title: 'Kesalahan',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#003778',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            @endif
            
            @if($errors->any() && !View::hasSection('inline_errors'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Tidak Valid',
                    html: '<ul class="text-left text-sm space-y-1">@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>',
                    confirmButtonColor: '#003778',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            @endif
        });
    </script>
    @yield('scripts')
</body>
</html>

