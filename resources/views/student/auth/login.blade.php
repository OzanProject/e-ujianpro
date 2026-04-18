@extends('layouts.guest')
@section('title', 'Login Peserta | ' . ($globalInstitution->name ?? \App\Models\Setting::getValue('app_name', 'E-Ujian PRO')))
@section('inline_errors', true)

@section('main_content')
<div class="w-full max-w-md">
    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-[0_24px_40px_rgba(25,28,29,0.06)] p-8 relative overflow-hidden group">
        <!-- Decorative element -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#003778] to-[#0a4da1]"></div>
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#191c1d] tracking-tight mb-2">Portal Peserta</h1>
            <p class="text-base text-[#424752]">Masukkan NIS Anda untuk memulai sesi ujian.</p>
        </div>

        <form class="space-y-6" action="{{ request()->route('subdomain') ? route('institution.student.login', request()->route('subdomain')) : route('student.login') }}" method="POST">
            @csrf

            <!-- NIS Field -->
            <div>
                <label class="block text-sm font-medium text-[#191c1d] mb-2" for="nis">NIS / Nomor Induk Siswa</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-[20px]">badge</span>
                    </div>
                    <input id="nis" name="nis" type="text" required 
                           value="{{ old('nis') }}"
                           class="input-edu" 
                           placeholder="Contoh: 123456" autofocus>
                </div>
                @error('nis')
                    <p class="error-text">
                        <span class="material-symbols-outlined text-[14px]">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label class="block text-sm font-medium text-[#191c1d] mb-2" for="password">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                    </div>
                    <input id="password" name="password" type="password" required
                           class="input-edu pr-10" 
                           placeholder="••••••••">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#191c1d] transition-colors" tabindex="-1">
                        <span id="eye-icon" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="error-text">
                        <span class="material-symbols-outlined text-[14px]">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-[#003778] border-[#c3c6d3] rounded focus:ring-[#003778]">
                <label for="remember" class="ml-2 block text-sm text-[#424752] cursor-pointer">
                    Ingat Saya
                </label>
            </div>

            <!-- Sign In Button -->
            <div>
                <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl font-semibold text-white bg-gradient-to-br from-[#003778] to-[#0a4da1] hover:opacity-90 transition-all duration-200 shadow-[0_4px_12px_rgba(0,55,120,0.15)] group">
                    <span>Mulai Ujian</span>
                    <span class="material-symbols-outlined ml-2 text-[18px] group-hover:translate-x-1 transition-transform">play_circle</span>
                </button>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-[#e1e3e4] pt-6">
            <a href="{{ request()->route('subdomain') ? route('institution.landing', request()->route('subdomain')) : url('/') }}"
               class="text-sm font-bold text-[#424752] hover:text-[#003778] transition inline-flex items-center gap-2 group">
                <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eye-icon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.innerText = "visibility_off";
        } else {
            passwordInput.type = "password";
            eyeIcon.innerText = "visibility";
        }
    }
</script>
@endsection
