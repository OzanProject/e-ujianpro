<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password | {{ $globalInstitution->name ?? 'E-Ujian PRO' }}</title>
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
             <img class="mx-auto h-16 w-auto object-contain" src="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('img/logo-placeholder.png') }}" alt="Logo">
            <h2 class="mt-4 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Reset Password
            </h2>
             <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan email Anda untuk menerima link reset password
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white/90 backdrop-blur-sm py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-white/50">
            
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Lupa password Anda? Tidak masalah. Beri tahu kami alamat email Anda dan kami akan mengirimkan tautan reset password.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
                @csrf

                <!-- Email Address -->
                 <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition duration-200">
                         @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:from-blue-500 hover:to-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition duration-200 transform hover:scale-[1.02]">
                        {{ __('Kirim Link Reset Password') }}
                    </button>
                </div>
            </form>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-2 text-gray-500">Ingat password?</span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">
                        Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
        
         <p class="mt-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ $globalInstitution->name ?? 'E-Ujian PRO' }}
        </p>
    </div>
</body>
</html>
