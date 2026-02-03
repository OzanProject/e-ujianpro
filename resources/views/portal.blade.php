<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Registrasi | {{ $globalInstitution->name ?? 'E-Ujian PRO' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 bg-[url('https://s3.ap-southeast-1.amazonaws.com/cdn.e-ujian.com/static-assets/images/bg-auth.png')] bg-cover bg-center min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-inter">
    <div class="sm:mx-auto sm:w-full sm:max-w-5xl">
        <div class="text-center mb-12">
            <img class="mx-auto h-24 w-auto object-contain drop-shadow-md mb-6" src="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('img/logo-placeholder.png') }}" alt="Logo">
            <h2 class="text-center text-4xl font-extrabold text-gray-900 tracking-tight">
                Selamat Datang di <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">Portal E-Ujian</span>
            </h2>
            <p class="mt-4 text-center text-lg text-gray-600 max-w-2xl mx-auto">
                Platform ujian online terpadu untuk sekolah dan institusi. Silakan pilih akses Anda di bawah ini.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-4 sm:px-0">
            <!-- Card Admin / Lembaga -->
            <div class="relative group bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/50 p-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl overflow-hidden cursor-default">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl group-hover:bg-blue-500/20 transition duration-500"></div>
                
                <div class="relative z-10 flex flex-col h-full text-center items-center">
                    <div class="h-20 w-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl flex items-center justify-center mb-6 shadow-inner group-hover:scale-110 transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Lembaga / Sekolah</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Ingin menyelenggarakan ujian sendiri? Daftarkan sekolah atau instansi Anda sekarang dan kelola ujian secara profesional.
                    </p>
                    <div class="mt-auto w-full">
                        <a href="{{ route('register.sekolah') }}" class="w-full inline-flex justify-center items-center px-6 py-4 border border-transparent text-lg font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 shadow-lg hover:shadow-blue-500/30">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Daftarkan Sekolah Baru
                        </a>
                        <p class="mt-4 text-xs text-gray-500">
                            *Khusus untuk Admin Sekolah / Instansi
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card Peserta -->
            <div class="relative group bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/50 p-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl overflow-hidden cursor-default">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-orange-500/10 blur-3xl group-hover:bg-orange-500/20 transition duration-500"></div>
                
                <div class="relative z-10 flex flex-col h-full text-center items-center">
                    <div class="h-20 w-20 bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl flex items-center justify-center mb-6 shadow-inner group-hover:scale-110 transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Peserta & Guru</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Sudah memiliki akun dari sekolah? Silakan login langsung untuk mengakses ujian atau mengelola kelas.
                    </p>
                    <div class="mt-auto w-full">
                        <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center px-6 py-4 border border-transparent text-lg font-bold rounded-xl text-white bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-200 shadow-lg hover:shadow-orange-500/30">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            Login ke Sistem
                        </a>
                        <p class="mt-4 text-xs text-gray-500">
                            *Login untuk Guru, Siswa, dan Operator
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-16 text-center">
            <p class="text-sm text-gray-500 font-medium">
                &copy; {{ date('Y') }} {{ $globalInstitution->name ?? 'E-Ujian PRO' }}. All rights reserved. <br>
                <span class="opacity-75 font-normal">Secure CBT Platform for Modern Education.</span>
            </p>
        </div>
    </div>
</body>
</html>
