@extends('layouts.guest')

@section('title', 'Verifikasi Email | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('header_title', 'Verifikasi Identitas')
@section('header_subtitle', 'Satu langkah lagi! Silakan verifikasi email Anda untuk mengaktifkan akses penuh.')

@section('content')
<div class="mb-8 p-6 rounded-3xl bg-indigo-50 border border-indigo-100 flex flex-col items-center text-center">
    <div class="h-16 w-16 rounded-2xl bg-white flex items-center justify-center text-indigo-600 shadow-sm mb-4">
        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    </div>
    <p class="text-sm font-bold text-indigo-900 leading-relaxed">
        Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan. Jika tidak menemukannya, kami dapat mengirimkan ulang.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 p-3 rounded-xl bg-green-100 border border-green-200 text-xs font-extrabold text-green-700 animate-in fade-in zoom-in">
            Link verifikasi baru telah dikirim ke alamat email Anda.
        </div>
    @endif
</div>

<div class="flex flex-col gap-4">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors uppercase text-xs tracking-widest">
            {{ __('Keluar (Log Out)') }}
        </button>
    </form>
</div>
@endsection

