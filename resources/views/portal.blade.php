@extends('layouts.guest')

@section('title', 'Portal Akses | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('card_width', 'sm:max-w-3xl')

@section('header_title', $globalInstitution->name ?? 'E-Ujian PRO')
@section('header_subtitle', 'Silakan pilih portal masuk sesuai dengan peran dan kebutuhan Anda.')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

    <!-- Portal Peserta / Siswa -->
    <a href="{{ route('student.login') }}"
       class="group flex flex-col items-center gap-4 p-8 rounded-2xl border border-gray-200 bg-white/70 hover:bg-white hover:border-indigo-300 hover:shadow-xl transition-all duration-300 text-center">
        
        <div style="width:4rem;height:4rem;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:1rem;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(79,70,229,0.3);" class="group-hover:scale-110 transition-transform duration-300">
            <svg style="width:2rem;height:2rem" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>

        <h3 class="text-xl font-bold text-gray-900">Portal Peserta</h3>
        <p class="text-sm text-gray-500 leading-relaxed">Masuk sebagai siswa untuk mengerjakan ujian dan melihat hasil penilaian.</p>

        <span class="mt-2 text-sm font-bold text-indigo-600 flex items-center gap-1 group-hover:gap-2 transition-all">
            Masuk Sekarang <span>&rarr;</span>
        </span>
    </a>

    <!-- Portal Admin / Guru -->
    <a href="{{ route('login') }}"
       class="group flex flex-col items-center gap-4 p-8 rounded-2xl border border-gray-200 bg-white/70 hover:bg-white hover:border-purple-300 hover:shadow-xl transition-all duration-300 text-center">

        <div style="width:4rem;height:4rem;background:linear-gradient(135deg,#7c3aed,#a21caf);border-radius:1rem;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(124,58,237,0.3);" class="group-hover:scale-110 transition-transform duration-300">
            <svg style="width:2rem;height:2rem" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>

        <h3 class="text-xl font-bold text-gray-900">Portal Pengajar</h3>
        <p class="text-sm text-gray-500 leading-relaxed">Masuk sebagai admin, guru, atau staf untuk mengelola ujian dan institusi.</p>

        <span class="mt-2 text-sm font-bold text-purple-600 flex items-center gap-1 group-hover:gap-2 transition-all">
            Masuk Sekarang <span>&rarr;</span>
        </span>
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
