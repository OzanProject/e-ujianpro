@extends('layouts.guest')

@section('title', 'Registrasi | ' . ($globalInstitution->name ?? 'E-Ujian PRO'))

@section('card_width', 'sm:max-w-2xl')

@section('header_title', 'Daftar Institusi')
@section('header_subtitle', 'Lengkapi data lembaga Anda untuk bergabung dengan ekosistem E-Ujian PRO.')

@section('content')
<form class="space-y-6" action="{{ route('register') }}" method="POST">
    @csrf
    <input type="hidden" name="role" value="admin_lembaga">

    {{-- ===== BAGIAN 1: Informasi Akun ===== --}}
    <div>
        <div class="flex items-center gap-3 mb-5">
            <div style="width:4px;height:1.5rem;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:9999px"></div>
            <h3 class="text-base font-bold text-gray-800">Informasi Akun Utama</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Nama --}}
            <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap Admin</label>
                <div class="flex items-center rounded-2xl border @error('name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(99,102,241,0.1)] transition-all">
                    <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                           class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                           placeholder="Nama lengkap Anda">
                </div>
                @error('name')<p class="mt-1 text-xs font-semibold text-red-600 flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div class="sm:col-span-2">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                <div class="flex items-center rounded-2xl border @error('email') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(99,102,241,0.1)] transition-all">
                    <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                           placeholder="nama@email.com">
                </div>
                @error('email')<p class="mt-1 text-xs font-semibold text-red-600 flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>@enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                <div class="flex items-center rounded-2xl border @error('password') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(99,102,241,0.1)] transition-all">
                    <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                           placeholder="Min. 8 karakter">
                </div>
                @error('password')<p class="mt-1 text-xs font-semibold text-red-600 flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>@enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                <div class="flex items-center rounded-2xl border border-gray-200 bg-gray-50 focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(99,102,241,0.1)] transition-all">
                    <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                           placeholder="Ulangi password">
                </div>
            </div>
        </div>
    </div>

    {{-- ===== BAGIAN 2: Detail Lembaga ===== --}}
    <div class="pt-2 border-t border-gray-100">
        <div class="flex items-center gap-3 mb-5 pt-4">
            <div style="width:4px;height:1.5rem;background:linear-gradient(135deg,#7c3aed,#a21caf);border-radius:9999px"></div>
            <h3 class="text-base font-bold text-gray-800">Detail Institusi / Sekolah</h3>
        </div>

        <div class="space-y-4">
            {{-- Nama Lembaga --}}
            <div>
                <label for="institution_name" class="block text-sm font-bold text-gray-700 mb-2">Nama Lembaga</label>
                <div class="flex items-center rounded-2xl border @error('institution_name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(99,102,241,0.1)] transition-all">
                    <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    <input id="institution_name" name="institution_name" type="text" required value="{{ old('institution_name') }}"
                           class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                           placeholder="Contoh: SMA Negeri 1 Jakarta">
                </div>
                @error('institution_name')<p class="mt-1 text-xs font-semibold text-red-600 flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>@enderror
            </div>

            {{-- Type & City side by side --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Jenis Lembaga --}}
                <div>
                    <label for="type" class="block text-sm font-bold text-gray-700 mb-2">Jenis Lembaga</label>
                    <div class="flex items-center rounded-2xl border @error('type') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white transition-all overflow-hidden">
                        <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </span>
                        <select id="type" name="type" required
                                class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 focus:outline-none border-0 ring-0 appearance-none cursor-pointer">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="SD" {{ old('type') == 'SD' ? 'selected' : '' }}>SD / Sederajat</option>
                            <option value="SMP" {{ old('type') == 'SMP' ? 'selected' : '' }}>SMP / Sederajat</option>
                            <option value="SMA" {{ old('type') == 'SMA' ? 'selected' : '' }}>SMA / Sederajat</option>
                            <option value="SMK" {{ old('type') == 'SMK' ? 'selected' : '' }}>SMK</option>
                            <option value="MA" {{ old('type') == 'MA' ? 'selected' : '' }}>MA / Madrasah</option>
                            <option value="PT" {{ old('type') == 'PT' ? 'selected' : '' }}>Perguruan Tinggi</option>
                            <option value="LK" {{ old('type') == 'LK' ? 'selected' : '' }}>Lembaga Kursus</option>
                            <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    @error('type')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Kota --}}
                <div>
                    <label for="city" class="block text-sm font-bold text-gray-700 mb-2">Kota / Kabupaten</label>
                    <div class="flex items-center rounded-2xl border @error('city') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(99,102,241,0.1)] transition-all">
                        <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <input id="city" name="city" type="text" required value="{{ old('city') }}"
                               class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                               placeholder="Contoh: Jakarta">
                    </div>
                    @error('city')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Subdomain --}}
            <div>
                <label for="subdomain" class="block text-sm font-bold text-gray-700 mb-2">Username / Subdomain</label>
                <div class="flex items-center rounded-2xl border @error('subdomain') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(99,102,241,0.1)] transition-all">
                    <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </span>
                    <input id="subdomain" name="subdomain" type="text" required value="{{ old('subdomain') }}"
                           class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                           placeholder="namasekolah">
                </div>
                @error('subdomain')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                <div class="mt-2 flex items-center gap-2 p-3 rounded-xl bg-indigo-50 border border-indigo-100 text-xs">
                    <svg class="h-4 w-4 flex-shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.102 1.101"/></svg>
                    <span class="text-gray-600">URL Portal: <strong class="text-indigo-700">{{ config('app.url') }}/<span id="url_preview">...</span></strong></span>
                </div>
            </div>

            {{-- WhatsApp --}}
            <div>
                <label for="whatsapp" class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp <span class="font-normal text-gray-400">(opsional)</span></label>
                <div class="flex items-center rounded-2xl border @error('whatsapp') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus-within:border-indigo-400 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(99,102,241,0.1)] transition-all">
                    <span class="pl-4 pr-2 text-gray-400 flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp') }}"
                           class="flex-1 bg-transparent py-3.5 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border-0 ring-0"
                           placeholder="Contoh: 0812xxxxxxxx">
                </div>
                @error('whatsapp')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="pt-2">
        <button type="submit" class="premium-btn w-full flex justify-center items-center gap-2 rounded-2xl px-6 py-4 text-base font-bold text-white transition duration-300">
            <span>Daftar Sekarang Secara Gratis</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </button>
        <p class="mt-3 text-center text-xs text-gray-400">Dengan mendaftar, Anda menyetujui Ketentuan Layanan kami.</p>
    </div>
</form>

<div class="mt-10 pt-8 border-t border-gray-100 text-center">
    <p class="text-sm text-gray-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 transition ml-1">Login di sini &rarr;</a>
    </p>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('subdomain').addEventListener('input', function() {
        let val = this.value.toLowerCase().replace(/[^a-z0-9\-]/g, '');
        this.value = val;
        document.getElementById('url_preview').textContent = val || '...';
    });
</script>
@endsection
