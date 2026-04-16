@extends('layouts.guest')

@section('title', 'Atur Ulang Password | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('header_title', 'Password Baru')
@section('header_subtitle', 'Tentukan password baru Anda yang kuat dan mudah diingat.')

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    {{-- Email (readonly) --}}
    <div>
        <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email Konfirmasi</label>
        <div class="flex items-center rounded-2xl border border-gray-200 bg-gray-100 opacity-70">
            <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input id="email" name="email" type="email" autocomplete="username" required
                   value="{{ old('email', $request->email) }}"
                   class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-500 focus:outline-none border-0 ring-0 cursor-not-allowed" readonly>
        </div>
    </div>

    {{-- Password Baru --}}
    <div>
        <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password Baru</label>
        <div class="flex items-center rounded-2xl border @error('password') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_4px_rgba(99,102,241,0.1)] transition-all">
            <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <input id="password" name="password" type="password" autocomplete="new-password" required
                   class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                   placeholder="Minimal 8 karakter">
        </div>
        @error('password')<p class="mt-1 text-xs font-semibold text-red-600 flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>@enderror
    </div>

    {{-- Konfirmasi Password --}}
    <div>
        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password Baru</label>
        <div class="flex items-center rounded-2xl border border-gray-200 bg-gray-50 focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_4px_rgba(99,102,241,0.1)] transition-all">
            <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </span>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                   class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                   placeholder="Ulangi password baru">
        </div>
    </div>

    {{-- Submit --}}
    <div class="pt-2">
        <button type="submit" class="premium-btn w-full flex justify-center items-center gap-2 rounded-2xl px-6 py-4 text-base font-bold text-white transition duration-300">
            <span>Simpan Password Baru</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </button>
    </div>
</form>
@endsection
