<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.5);
            --font-main: 'Inter', sans-serif;
            --font-heading: 'Outfit', sans-serif;
        }

        body { 
            font-family: var(--font-main);
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: var(--font-heading);
        }

        .premium-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: 
                radial-gradient(circle at 0% 0%, rgba(79, 70, 229, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 100% 100%, rgba(124, 58, 237, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(147, 51, 234, 0.05) 0%, transparent 70%),
                #f8fafc;
            background-attachment: fixed;
        }

        /* Modern Mesh Gradient Fallback */
        .premium-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
            z-index: -1;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.1),
                inset 0 0 0 1px rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            box-shadow: 
                0 35px 60px -15px rgba(0, 0, 0, 0.15),
                inset 0 0 0 1px rgba(255, 255, 255, 0.5);
        }

        /* Force input focus styling to overcome browser defaults */
        input:focus {
            box-shadow: none !important;
            border-color: transparent !important;
            outline: none !important;
        }

        /* For inputs inside flex-icon containers, let the parent handle focus styling */
        .focus-within\:border-indigo-400:focus-within input:focus {
            box-shadow: none !important;
        }

        .premium-btn {
            background: var(--primary-gradient);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
        }

        .premium-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
            filter: brightness(110%);
        }

        .premium-btn:active {
            transform: translateY(0);
        }

        /* Robust Icon Wrapper - FIXED: icon stays locked to input height, not container */
        .input-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: 0;
            top: 0;
            height: 3.75rem; /* Matches py-4 input height: 1rem top + 1rem bottom + ~1.75 line-height */
            width: 3.5rem;
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.3s ease;
            z-index: 20;
        }

        .group:focus-within .input-icon-wrapper {
            color: #4f46e5;
        }

        /* Global Reset for Input Padding */
        .robust-input {
            padding-left: 3.5rem !important;
            padding-right: 1.25rem !important;
        }

        /* Pulse for verification messages */
        .animate-soft-pulse {
            animation: soft-pulse 2s infinite;
        }

        @keyframes soft-pulse {
            0% { opacity: 0.9; }
            50% { opacity: 1; transform: scale(1.01); }
            100% { opacity: 0.9; }
        }
    </style>
    @yield('styles')
</head>
<body class="text-gray-900 antialiased min-h-screen flex flex-col items-center justify-center p-4">
    <div class="premium-bg"></div>
    
    <main class="w-full max-w-6xl mx-auto flex flex-col items-center">
        <!-- Logo Area -->
        <div class="text-center mb-8 animate-in fade-in slide-in-from-top-4 duration-700">
            <a href="/" class="inline-block mb-4">
                @php
                    $rawLogo = \App\Models\Setting::getValue('app_logo', 'img/logo-placeholder.png');
                    $logoPath = url($rawLogo);
                @endphp

                <img class="h-24 w-auto object-contain drop-shadow-2xl transition transform hover:scale-110 duration-500"
                     src="{{ $logoPath }}" alt="Logo"
                     id="main-logo"
                     onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">

                {{-- Fallback: shown when logo fails to load --}}
                <div id="logo-fallback" style="display:none" class="h-24 w-24 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-3xl items-center justify-center shadow-2xl transform hover:rotate-6 transition duration-500 mx-auto">
                    <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                </div>
            </a>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl drop-shadow-sm">
                @yield('header_title', $globalInstitution->name ?? 'E-Ujian PRO')
            </h1>
            <p class="mt-4 text-lg text-gray-600 font-medium max-w-xl mx-auto italic opacity-90">
                @yield('header_subtitle', 'Secure Application Architecture for Modern Education.')
            </p>
        </div>

        <!-- Content Card -->
        <div class="w-full @yield('card_width', 'sm:max-w-md') px-0 sm:px-4">
            <div class="glass-card py-10 px-6 sm:px-12 rounded-[2.5rem] relative overflow-hidden group">
                <!-- Abstract blobs for visual depth -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-700"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10 transition-all duration-700"></div>
                
                <div class="relative z-10 animate-in fade-in zoom-in-95 duration-700 delay-150">
                    @yield('content')
                </div>
            </div>
            
            @yield('footer')
            
            <p class="mt-12 text-center text-sm text-gray-400 font-medium tracking-wide">
                &copy; {{ date('Y') }} {{ $globalInstitution->name ?? 'E-Ujian PRO' }} <br>
                <span class="text-[10px] uppercase opacity-50 block mt-1">Platform Dikembangkan oleh Ozan Project</span>
            </p>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: 'rounded-3xl shadow-xl' }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#4f46e5',
                    customClass: { popup: 'rounded-3xl shadow-xl' }
                });
            @endif
            
            @if($errors->any() && !View::hasSection('inline_errors'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Tidak Valid',
                    html: '<ul class="text-left text-sm space-y-1">@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>',
                    confirmButtonColor: '#4f46e5',
                    customClass: { popup: 'rounded-3xl shadow-xl' }
                });
            @endif
        });
    </script>
    @yield('scripts')
</body>
</html>

