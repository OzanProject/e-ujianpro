<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $globalInstitution->name ?? 'E-Ujian PRO - Platform Ujian Online Sekolah Modern' }}</title>
    <meta name="description" content="Platform CBT Terintegrasi untuk Sekolah dan Lembaga Pendidikan. Aman, Cepat, dan Mudah digunakan.">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('favicon.png') }}">
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
                     <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <img class="h-10 w-auto object-contain" src="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('img/logo-placeholder.png') }}" alt="Logo">
                        <span class="text-xl font-bold text-gray-900 hidden sm:block">{{ $globalInstitution->name ?? 'E-Ujian PRO' }}</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-blue-600 font-medium transition">Keunggulan</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-blue-600 font-medium transition">Cara Kerja</a>
                    <a href="{{ route('student.login') }}" class="text-gray-600 hover:text-blue-600 font-medium transition">Masuk Siswa</a>
                    <a href="{{ route('portal') }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition shadow-lg hover:shadow-blue-500/30 transform hover:-translate-y-0.5">
                        Daftar Sekarang
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
            <a href="#features" class="block text-gray-600 hover:text-blue-600 font-medium">Keunggulan</a>
            <a href="#how-it-works" class="block text-gray-600 hover:text-blue-600 font-medium">Cara Kerja</a>
            <a href="{{ route('student.login') }}" class="block text-gray-600 hover:text-blue-600 font-medium">Masuk Siswa</a>
            <a href="{{ route('portal') }}" class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold">Daftar Sekarang</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
         <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 rounded-full bg-blue-400/10 blur-3xl"></div>
         <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 rounded-full bg-indigo-400/10 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-wider mb-6 border border-blue-100 shadow-sm">
                    Platform Ujian Online #1
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-6 tracking-tight">
                    Digitalisasi Ujian Sekolah dengan <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">Mudah & Aman</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-600 mb-10 leading-relaxed">
                    Solusi CBT (Computer Based Test) lengkap untuk sekolah modern. Kelola bank soal, laksanakan ujian, dan analisis nilai siswa dalam satu platform terintegrasi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('portal') }}" class="inline-flex justify-center items-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition shadow-xl hover:shadow-blue-600/30 transform hover:-translate-y-1">
                        Coba Gratis Sekarang
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-8 py-4 text-lg font-bold rounded-xl text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition shadow-md hover:shadow-lg">
                        Login Administrator
                    </a>
                </div>
                
                 <div class="mt-12 flex justify-center items-center gap-8 text-gray-400 grayscale opacity-70">
                    <!-- Placeholder Logos or Stats -->
                    <div class="flex flex-col items-center">
                         <span class="text-2xl font-bold text-gray-800">100+</span>
                         <span class="text-xs">Sekolah</span>
                    </div>
                     <div class="h-8 w-px bg-gray-300"></div>
                    <div class="flex flex-col items-center">
                         <span class="text-2xl font-bold text-gray-800">50k+</span>
                         <span class="text-xs">Siswa Aktif</span>
                    </div>
                     <div class="h-8 w-px bg-gray-300"></div>
                     <div class="flex flex-col items-center">
                         <span class="text-2xl font-bold text-gray-800">1M+</span>
                         <span class="text-xs">Ujian Selesai</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Unggulan Kami</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Dirancang khusus untuk kebutuhan sekolah Indonesia, mendukung AKM, Ujian Sekolah, hingga Penilaian Harian.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-xl transition duration-300 group">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                         <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Multi-Device</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Siswa dapat mengerjakan ujian melalui Laptop, Tablet, maupun Smartphone dengan tampilan yang responsif dan ringan.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-xl transition duration-300 group">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                         <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Sistem Anti-Curang</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Dilengkapi fitur pengacak soal dan jawaban, serta timer otomatis untuk menjaga integritas hasil ujian siswa.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-xl transition duration-300 group">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                         <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Bank Soal Lengkap</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Mendukung soal Pilihan Ganda, Essay, dan Import massal dari Excel/Word. Manajemen bank soal jadi sangat mudah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us / Detail Section -->
    <section id="how-it-works" class="py-20 bg-gray-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="w-full md:w-1/2">
                    <img src="https://s3.ap-southeast-1.amazonaws.com/cdn.e-ujian.com/static-assets/themes/spring/images/resource/banner-image-6-min.png" alt="Ilustrasi Ujian" class="w-full h-auto drop-shadow-2xl rounded-3xl transform hover:scale-105 transition duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    <span class="text-blue-600 font-bold uppercase tracking-wider text-sm">KENAPA KAMI?</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 mb-6">Fokus pada Kualitas Penilaian</h2>
                    <p class="text-gray-600 mb-6 text-lg">
                        Kami memahami bahwa administrasi ujian seringkali merepotkan. E-Ujian PRO hadir untuk memangkas proses manual tersebut.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mt-1">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="ml-3 text-gray-700 font-medium">Analisis Butir Soal Otomatis</span>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mt-1">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="ml-3 text-gray-700 font-medium">Cetak Kartu Ujian & Daftar Hadir</span>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mt-1">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="ml-3 text-gray-700 font-medium">Nilai Langsung Keluar Realtime</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 relative">
        <div class="absolute inset-0 bg-gradient-to-tr from-blue-700 to-indigo-800"></div>
         <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h2 class="text-3xl md:text-5xl font-bold mb-6 tracking-tight">Siap Transformasi Sekolah Anda?</h2>
            <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">Bergabung dengan ratusan sekolah lainnya yang telah beralih ke ujian digital. Hemat kertas, hemat waktu, hasil akurat.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.sekolah') }}" class="px-8 py-4 bg-white text-blue-700 font-bold rounded-xl shadow-lg hover:bg-gray-100 transition transform hover:-translate-y-1">
                    Daftarkan Sekolah Gratis
                </a>
                <a href="https://wa.me/{{ \App\Models\Setting::getValue('app_whatsapp', '6281321794279') }}" target="_blank" class="px-8 py-4 bg-blue-600 bg-opacity-30 border border-blue-400 text-white font-bold rounded-xl hover:bg-opacity-50 transition">
                    Hubungi Sales
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <span class="text-2xl font-bold text-white block mb-4">{{ $globalInstitution->name ?? 'E-Ujian PRO' }}</span>
                    <p class="text-gray-400 max-w-sm">
                        Platform pendidikan terdepan yang membantu sekolah mendigitalkan proses evaluasi belajar mengajar dengan teknologi terkini.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4 uppercase text-sm tracking-wider">Akses Cepat</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('portal') }}" class="hover:text-white transition">Portal Pendaftaran</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Login Admin</a></li>
                        <li><a href="{{ route('student.login') }}" class="hover:text-white transition">Login Siswa</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4 uppercase text-sm tracking-wider">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('page.show', 'privacy') }}" class="hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('page.show', 'terms') }}" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('page.show', 'contact') }}" class="hover:text-white transition">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ $globalInstitution->name ?? 'E-Ujian PRO' }}. All rights reserved.
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
