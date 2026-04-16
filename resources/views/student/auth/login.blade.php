@extends('layouts.guest')

@section('title', 'Login Peserta | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('header_title', 'Portal Peserta Ujian')
@section('header_subtitle', 'Masukkan NIS dan Password Anda untuk memulai sesi ujian.')

@section('content')
<form class="space-y-5" action="{{ request()->route('subdomain') ? route('institution.student.login', request()->route('subdomain')) : route('student.login') }}" method="POST">
    @csrf

    <!-- NIS / Student ID -->
    <div>
        <label for="nis" class="block text-sm font-bold text-gray-700 mb-2">NIS / Nomor Induk Siswa</label>
        <div class="flex items-center rounded-2xl border @error('nis') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror transition-all duration-200 focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]">
            <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </span>
            <input id="nis" name="nis" type="text" required
                   value="{{ old('nis') }}"
                   class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                   placeholder="Masukkan NIS Anda" autofocus>
        </div>
        @error('nis')
            <p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-red-600">
                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password</label>
        <div class="flex items-center rounded-2xl border @error('password') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror transition-all duration-200 focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]">
            <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </span>
            <input id="password" name="password" type="password" required
                   class="flex-1 bg-transparent py-3.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                   placeholder="••••••••">
            <button type="button" onclick="togglePassword()" class="px-4 text-gray-400 hover:text-indigo-600 transition flex-shrink-0">
                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
        </div>
        @error('password')
            <p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-red-600">
                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="flex items-center pt-1">
        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
        <label for="remember" class="ml-3 text-sm font-bold text-gray-500 cursor-pointer hover:text-indigo-600 transition">Ingat Saya</label>
    </div>

    <!-- Submit -->
    <div class="pt-2">
        <button type="submit" class="premium-btn w-full flex justify-center items-center gap-2 rounded-2xl px-6 py-4 text-base font-bold text-white transition duration-300">
            <span>Mulai Sesi Ujian</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
        </button>
    </div>
</form>

<div class="mt-10 pt-8 border-t border-gray-100 flex justify-center">
    <a href="{{ request()->route('subdomain') ? route('institution.landing', request()->route('subdomain')) : url('/') }}"
       class="text-sm font-bold text-gray-400 hover:text-indigo-600 transition inline-flex items-center gap-2 group">
        <svg class="w-4 h-4 transform transition group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Beranda
    </a>
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
