@extends('layouts.guest')

@section('title', 'Konfirmasi Password | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('header_title', 'Keamanan Lanjutan')
@section('header_subtitle', 'Tindakan ini sensitif. Silakan konfirmasi password Anda untuk melanjutkan.')

@section('content')
<div class="mb-8 p-4 rounded-2xl bg-amber-50 border border-amber-100 flex items-start">
    <svg class="h-6 w-6 text-amber-600 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
    </svg>
    <p class="text-xs font-bold text-amber-800 leading-relaxed">
        Demi keamanan data institusi, kami perlu memverifikasi identitas Anda kembali sebelum melanjutkan ke area sensitif.
    </p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
    @csrf

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-bold text-gray-700 mb-2 ml-1">Password Anda</label>
        <div class="relative group">
            <div class="input-icon-wrapper">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                 </svg>
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="robust-input block w-full rounded-2xl border-gray-200 bg-gray-50/50 py-4 text-gray-900 shadow-sm transition duration-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 @error('password') border-red-300 @enderror" 
                   placeholder="••••••••" autofocus>
        </div>
    </div>

    <div>
        <button type="submit" class="premium-btn w-full flex justify-center items-center rounded-2xl px-6 py-4.5 text-lg font-bold text-white transition duration-300">
            <span>Konfirmasi & Lanjutkan</span>
            <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </button>
    </div>
</form>
@endsection
