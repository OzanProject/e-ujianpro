@extends('layouts.guest')

@section('title', 'Portal Akses | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('card_width', 'sm:max-w-3xl')

@section('header_title', $globalInstitution->name ?? 'E-Ujian PRO')
@section('header_subtitle', 'Silakan pilih portal masuk sesuai dengan peran dan kebutuhan Anda.')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-8">

    <!-- Portal Peserta / Siswa -->
    <a href="{{ route('student.login') }}"
       class="group relative flex flex-col items-center gap-6 p-10 rounded-[2.5rem] border border-gray-100 bg-white shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-center overflow-hidden">
        
        <!-- Decorative background blob -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-50 rounded-full blur-3xl group-hover:bg-indigo-100 transition-all duration-500"></div>

        <div class="relative w-24 h-24 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-3xl flex items-center justify-center shadow-xl shadow-indigo-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>

        <div class="relative">
            <h3 class="text-2xl font-black text-gray-900 mb-2">Siswa</h3>
            <p class="text-sm text-gray-500 leading-relaxed px-2">Masuk untuk mengerjakan ujian & pantau hasil belajar Anda secara realtime.</p>
        </div>

        <div class="pt-2">
            <span class="inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-indigo-50 text-indigo-700 text-sm font-bold group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                Masuk Member &rarr;
            </span>
        </div>
    </a>

    <!-- Portal Admin / Guru -->
    <a href="{{ route('login') }}"
       class="group relative flex flex-col items-center gap-6 p-10 rounded-[2.5rem] border border-gray-100 bg-white shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-center overflow-hidden">
        
        <!-- Decorative background blob -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-purple-50 rounded-full blur-3xl group-hover:bg-purple-100 transition-all duration-500"></div>

        <div class="relative w-24 h-24 bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-3xl flex items-center justify-center shadow-xl shadow-purple-200 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500">
            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>

        <div class="relative">
            <h3 class="text-2xl font-black text-gray-900 mb-2">Lembaga</h3>
            <p class="text-sm text-gray-500 leading-relaxed px-2">Kelola bank soal, sesi ujian, dan administrasi sekolah dalam satu platform.</p>
        </div>

        <div class="pt-2">
            <span class="inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-purple-50 text-purple-700 text-sm font-bold group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                Masuk Staf &rarr;
            </span>
        </div>
    </a>

</div>

{{-- Tombol Register langsung di dalam content (bukan footer) agar selalu terlihat --}}
<div class="mt-8 pt-6 border-t border-gray-100 text-center">
    <p class="text-sm text-gray-500 mb-3">Belum memiliki akun lembaga?</p>
    <a href="{{ route('register.sekolah') }}"
       class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border-2 border-indigo-200 text-indigo-600 font-bold text-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-200">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Daftar Lembaga Baru Gratis
    </a>
</div>
@endsection
