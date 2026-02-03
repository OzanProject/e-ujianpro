<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ $globalInstitution->name ?? 'E-Ujian PRO' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 bg-[url('https://s3.ap-southeast-1.amazonaws.com/cdn.e-ujian.com/static-assets/images/bg-auth.png')] bg-cover bg-center min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <img class="mx-auto h-20 w-auto object-contain drop-shadow-sm mb-6" src="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('img/logo-placeholder.png') }}" alt="Logo">
            <h2 class="text-center text-3xl font-extrabold text-gray-900 tracking-tight">
                {{ $globalInstitution->name ?? 'E-Ujian PRO' }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Silakan masuk untuk melanjutkan akses
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white/90 backdrop-blur-xl py-8 px-6 shadow-2xl sm:rounded-3xl border border-white/60 sm:px-10 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 rounded-full bg-blue-400/10 blur-2xl"></div>
            
            <form class="space-y-6 relative z-10" action="{{ request()->route('subdomain') ? url(request()->route('subdomain') . '/login') : route('login') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email / Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                             </svg>
                        </div>
                        <input id="email" name="email" type="text" autocomplete="username" required value="{{ old('email') }}" class="block w-full pl-10 rounded-xl border-gray-300 bg-gray-50/50 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200" placeholder="Masukkan ID Pengguna">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                    </div>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                             </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full pl-10 pr-10 rounded-xl border-gray-300 bg-gray-50/50 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200" placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 hover:text-blue-600 text-gray-400 transition cursor-pointer z-20">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                         @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 transition cursor-pointer">
                        <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer">Ingat Saya</label>
                    </div>

                    <div class="text-sm">
                         @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">Lupa password?</a>
                        @endif
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-3.5 text-sm font-bold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition duration-200 transform hover:scale-[1.01]">
                        Masuk Sekarang
                    </button>
                </div>
            </form>
            
             <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-3 text-gray-500 rounded-full border border-gray-100 shadow-sm">Atau</span>
                    </div>
                </div>
                <!-- Optional: Secondary link -->
                 <div class="mt-6 text-center">
                     <a href="{{ request()->route('subdomain') ? route('institution.landing', request()->route('subdomain')) : route('portal') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">
                        &larr; Kembali ke Sekolah
                    </a>
                 </div>
            </div>
            
        </div>
        
        <p class="mt-8 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ $globalInstitution->name ?? 'E-Ujian PRO' }}. All rights reserved.<br>
            Secure Application System.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle Password
        function togglePassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        // SweetAlert Notifications
        document.addEventListener('DOMContentLoaded', function () {
            // Success Message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            // Error Message
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                });
            @endif

            // Status Message (e.g. Password Reset)
            @if(session('status'))
                let title = 'Info';
                let text = "{{ session('status') }}";
                let icon = 'info';

                if (text === 'verification-link-sent') {
                    title = 'Terkirim!';
                    text = 'Link verifikasi baru telah dikirim ke email Anda.';
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

            // Validation Errors (e.g. Wrong Password)
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    html: '<ul class="text-left text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                });
            @endif
        });
    </script>
</body>
</html>
