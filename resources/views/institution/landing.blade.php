<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $institution->name }} | Portal Ujian Sekolah</title>
    <meta name="description" content="Portal Ujian Resmi {{ $institution->name }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $institution->logo ? asset('storage/' . $institution->logo) : asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .hero-pattern {
            background-color: #f9fafb;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e5e7eb' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                     <a href="#" class="flex items-center gap-3">
                        <img class="h-10 w-auto object-contain" src="{{ $institution->logo ? asset('storage/' . $institution->logo) : asset('dist/img/AdminLTELogo.png') }}" alt="Logo">
                        <span class="text-xl font-bold text-gray-900 hidden sm:block">{{ $institution->name }}</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('institution.login', $institution->subdomain) }}" class="text-gray-600 hover:text-blue-600 font-medium transition">Login Guru, Staf & Pengawas</a>
                    <a href="{{ route('institution.student.login', $institution->subdomain) }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition shadow-lg hover:shadow-blue-500/30 transform hover:-translate-y-0.5">
                        Login Siswa
                    </a>
                </div>
                 <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="mobile-menu-button outline-none p-2 text-gray-600 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="hidden mobile-menu md:hidden bg-white border-t border-gray-100 px-4 py-4 space-y-3 shadow-lg">
            <a href="{{ route('institution.login', $institution->subdomain) }}" class="block text-gray-600 hover:text-blue-600 font-medium">Login Guru, Staf & Pengawas</a>
            <a href="{{ route('institution.student.login', $institution->subdomain) }}" class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold">Login Siswa</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
         <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 rounded-full bg-blue-400/10 blur-3xl"></div>
         <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 rounded-full bg-indigo-400/10 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-wider mb-6 border border-blue-100 shadow-sm">
                    Portal Akademik Resmi
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-6 tracking-tight">
                    Selamat Datang di <br><span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">{{ $institution->name }}</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-600 mb-10 leading-relaxed">
                    Sistem Ujian Berbasis Komputer (CBT) dan Manajemen Sekolah Terintegrasi. Silakan masuk untuk mengakses layanan.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('institution.student.login', $institution->subdomain) }}" class="inline-flex justify-center items-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition shadow-xl hover:shadow-blue-600/30 transform hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Masuk Sebagai Siswa
                    </a>
                    <a href="{{ route('institution.login', $institution->subdomain) }}" class="inline-flex justify-center items-center px-8 py-4 text-lg font-bold rounded-xl text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition shadow-md hover:shadow-lg">
                        Login Guru, Staf & Pengawas
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                 <img class="h-12 w-auto mx-auto mb-4 object-contain" src="{{ $institution->logo ? asset('storage/' . $institution->logo) : asset('dist/img/AdminLTELogo.png') }}" alt="Logo">
                 <h3 class="font-bold text-gray-900 text-lg">{{ $institution->name }}</h3>
                 @if($institution->address)
                    <p class="text-gray-500 mt-2">{{ $institution->address }}</p>
                 @endif
                 <div class="mt-8 text-sm text-gray-400">
                    &copy; {{ date('Y') }} {{ $institution->name }}. Powered by E-Ujian PRO.
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        const btn = document.querySelector("button.mobile-menu-button");
        const menu = document.querySelector(".mobile-menu");

        btn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        });
    </script>
</body>
</html>
