@extends('layouts.guest')

@section('title', 'Login Admin | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('header_title', 'Administrasi Sistem')
@section('header_subtitle', 'Silakan masuk untuk mengelola ujian dan institusi Anda.')

@section('content')
<form class="space-y-6" action="{{ request()->route('subdomain') ? url(request()->route('subdomain') . '/login') : route('login') }}" method="POST">
    @csrf

    <!-- Email / Username -->
    <div class="group">
        <label for="email" class="block text-sm font-bold text-gray-700 mb-2 transition-colors group-focus-within:text-indigo-600">Email atau Username</label>
        <div class="relative flex items-center rounded-2xl border border-gray-200 bg-gray-50/50 hover:border-gray-300 focus-within:border-indigo-500 focus-within:bg-white focus-within:shadow-[0_0_0_4px_rgba(99,102,241,0.1)] transition-all duration-300">
            <div class="flex items-center justify-center w-12 h-12 ml-1 text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <input id="email" name="email" type="text" autocomplete="username" required
                   value="{{ old('email') }}"
                   class="flex-1 bg-transparent py-4 pr-6 text-sm text-gray-900 border-0 focus:ring-0 placeholder-gray-400"
                   placeholder="Masukkan Email atau Username">
        </div>
        @error('email')
            <p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-red-600 animate-in fade-in slide-in-from-left-2 duration-300">
                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Password -->
    <div class="group">
        <div class="flex items-center justify-between mb-2">
            <label for="password" class="block text-sm font-bold text-gray-700 transition-colors group-focus-within:text-indigo-600">Kata Sandi</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-500 transition">Lupa password?</a>
            @endif
        </div>
        <div class="relative flex items-center rounded-2xl border border-gray-200 bg-gray-50/50 hover:border-gray-300 focus-within:border-indigo-500 focus-within:bg-white focus-within:shadow-[0_0_0_4px_rgba(99,102,241,0.1)] transition-all duration-300">
            <div class="flex items-center justify-center w-12 h-12 ml-1 text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <input id="password" name="password" type="password" autocomplete="current-password" required
                   class="flex-1 bg-transparent py-4 text-sm text-gray-900 border-0 focus:ring-0 placeholder-gray-400"
                   placeholder="••••••••">
            <button type="button" onclick="togglePassword()" class="px-4 text-gray-400 hover:text-indigo-600 transition outline-none">
                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
        </div>
        @error('password')
            <p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-red-600 animate-in fade-in slide-in-from-left-2 duration-300">
                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Options -->
    <div class="flex items-center justify-between pt-1">
        <label class="flex items-center cursor-pointer group">
            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition cursor-pointer">
            <span class="ml-2 text-sm font-bold text-gray-500 group-hover:text-gray-700 transition">Ingat saya</span>
        </label>
    </div>

    <!-- CTA -->
    <div class="pt-4">
        <button type="submit" class="premium-btn w-full flex justify-center items-center gap-2 rounded-2xl px-6 py-4 text-base font-bold text-white transition duration-300 group">
            <span>Masuk ke Dashboard</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
        </button>
    </div>
</form>

<div class="mt-10 pt-8 border-t border-gray-100 flex flex-col items-center gap-4">
    <a href="{{ request()->route('subdomain') ? route('institution.landing', request()->route('subdomain')) : route('portal') }}"
       class="text-sm font-bold text-gray-400 hover:text-indigo-600 transition inline-flex items-center gap-2 group">
        <svg class="w-4 h-4 transform transition group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Beranda
    </a>

    @if (Route::has('register.sekolah'))
    <div class="flex items-center gap-3">
        <div class="h-px w-8 bg-gray-200"></div>
        <span class="text-xs text-gray-400">atau</span>
        <div class="h-px w-8 bg-gray-200"></div>
    </div>
    <a href="{{ route('register.sekolah') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-500 transition inline-flex items-center gap-1">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Daftar Lembaga Baru
    </a>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function togglePassword() {
        var x = document.getElementById("password");
        var icon = document.getElementById("eye-icon");
        if (x.type === "password") {
            x.type = "text";
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
        } else {
            x.type = "password";
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
        }
    }
</script>
@endsection
