@extends('layouts.guest')

@section('title', 'Lupa Password | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('header_title', 'Lupa Password?')
@section('header_subtitle', 'Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password.')

@section('content')

{{-- Notifikasi sukses --}}
@if (session('status'))
    <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-green-50 border border-green-200 text-sm font-semibold text-green-700">
        <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('status') }}
    </div>
@endif

<div class="mb-6 flex items-start gap-3 p-4 rounded-2xl bg-blue-50 border border-blue-100 text-xs font-medium text-blue-800">
    <svg class="h-5 w-5 flex-shrink-0 text-blue-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    Pastikan email yang Anda masukkan terdaftar di sistem kami untuk menerima instruksi selanjutnya.
</div>

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <div>
        <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Alamat Email Terdaftar</label>
        <div class="flex items-center rounded-2xl border @error('email') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror transition-all duration-200 focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]">
            <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </span>
            <input id="email" name="email" type="email" autocomplete="email" required
                   value="{{ old('email') }}"
                   class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                   placeholder="nama@email.com" autofocus>
        </div>
        @error('email')
            <p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-red-600">
                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="pt-1">
        <button type="submit" class="premium-btn w-full flex justify-center items-center gap-2 rounded-2xl px-6 py-4 text-base font-bold text-white transition duration-300">
            <span>Kirim Link Reset Password</span>
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </button>
    </div>
</form>

<div class="mt-10 pt-8 border-t border-gray-100 flex justify-center">
    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-400 hover:text-indigo-600 transition inline-flex items-center gap-2 group">
        <svg class="w-4 h-4 transform transition group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Login
    </a>
</div>
@endsection
