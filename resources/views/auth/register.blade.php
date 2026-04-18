@extends('layouts.guest')
@section('title', 'Registrasi | ' . ($globalInstitution->name ?? \App\Models\Setting::getValue('app_name', 'E-Ujian PRO')))
@section('inline_errors', true)

@section('main_content')
<div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center relative z-10 py-12">
    <!-- Left Side: Editorial Introduction -->
    <div class="lg:col-span-5 flex flex-col space-y-6 lg:pr-8 animate-in fade-in slide-in-from-left-8 duration-1000">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#edeeef]/80 backdrop-blur w-fit">
            <span class="material-symbols-outlined text-[#003778] text-sm">verified_user</span>
            <span class="text-xs font-bold text-[#191c1d] tracking-wide uppercase">Institutional Access</span>
        </div>
        <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tight text-[#191c1d] leading-tight">
            {{ \App\Models\Setting::getValue('hero_title', 'Digitalisasi Ujian Sekolah dengan Mudah & Aman') }}
        </h1>
        <p class="text-lg text-[#424752] leading-relaxed">
            {{ \App\Models\Setting::getValue('hero_subtitle', 'Solusi CBT (Computer Based Test) lengkap untuk sekolah modern. Kelola bank soal, laksanakan ujian, dan analisis nilai siswa dalam satu platform terintegrasi.') }}
        </p>
        <div class="pt-8 flex items-center gap-4">
            <div class="flex -space-x-3">
                <img src="https://ui-avatars.com/api/?name=Institution+1&background=003778&color=fff" alt="Inst" class="w-10 h-10 rounded-full border-2 border-white object-cover">
                <img src="https://ui-avatars.com/api/?name=Institution+2&background=0a4da1&color=fff" alt="Inst" class="w-10 h-10 rounded-full border-2 border-white object-cover">
                <img src="https://ui-avatars.com/api/?name=Institution+3&background=bac7de&color=191c1d" alt="Inst" class="w-10 h-10 rounded-full border-2 border-white object-cover">
            </div>
            <div class="text-sm font-medium text-[#424752]">
                Dipercaya oleh <strong>100+</strong> institusi pendidikan di seluruh Indonesia.
            </div>
        </div>
    </div>

    <!-- Right Side: Registration Form Card -->
    <div class="lg:col-span-7 w-full max-w-2xl mx-auto lg:max-w-none animate-in fade-in slide-in-from-right-8 duration-1000 delay-150">
        <div class="bg-white rounded-2xl shadow-[0_24px_40px_rgba(25,28,29,0.06)] p-8 lg:p-10 border border-[#e1e3e4]/50 relative overflow-hidden">
            <div class="mb-8 border-b-0">
                <h2 class="text-2xl font-bold text-[#191c1d] tracking-tight mb-2">Pendaftaran Lembaga</h2>
                <p class="text-sm text-[#424752]">Lengkapi detail institusi Anda untuk memulai.</p>
            </div>

            <form action="{{ route('register') }}" class="space-y-6" method="POST">
                @csrf
                <input type="hidden" name="role" value="admin_lembaga">

                {{-- ACCOUNT INFO SECTION --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#191c1d]" for="name">Nama Admin</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">badge</span>
                            </div>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                   class="input-edu" placeholder="Nama Lengkap">
                        </div>
                        @error('name')<p class="error-text"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#191c1d]" for="email">Email Institusi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">mail</span>
                            </div>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                   class="input-edu" placeholder="admin@sekolah.sch.id">
                        </div>
                        @error('email')<p class="error-text"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#191c1d]" for="password">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </div>
                            <input id="password" name="password" type="password" required
                                   class="input-edu" placeholder="••••••••">
                        </div>
                        @error('password')<p class="error-text"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#191c1d]" for="password_confirmation">Konfirmasi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="input-edu" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                {{-- INSTITUTION INFO SECTION --}}
                <div class="pt-4 border-t border-[#e1e3e4] space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#191c1d]" for="institution_name">Nama Lembaga</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">account_balance</span>
                            </div>
                            <input id="institution_name" name="institution_name" type="text" value="{{ old('institution_name') }}" required
                                   class="input-edu" placeholder="Contoh: SMA Negeri 1 Jakarta">
                        </div>
                        @error('institution_name')<p class="error-text"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-[#191c1d]" for="type">Jenis Lembaga</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 z-10">
                                    <span class="material-symbols-outlined text-[20px]">category</span>
                                </div>
                                <select id="type" name="type" required class="input-edu appearance-none relative z-20 bg-transparent">
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
                            @error('type')<p class="error-text"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-[#191c1d]" for="city">Kota / Kabupaten</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">location_on</span>
                                </div>
                                <input id="city" name="city" type="text" value="{{ old('city') }}" required
                                       class="input-edu" placeholder="Contoh: Jakarta">
                            </div>
                            @error('city')<p class="error-text"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#191c1d]" for="subdomain">Subdomain / Username Portal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">language</span>
                            </div>
                            <input id="subdomain" name="subdomain" type="text" value="{{ old('subdomain') }}" required
                                   class="input-edu" placeholder="namasekolah">
                        </div>
                        @error('subdomain')<p class="error-text"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                        <div class="mt-2 text-xs text-[#424752] bg-[#f8f9fa] p-3 rounded-xl border border-[#e1e3e4]">
                            URL Portal: <strong class="text-[#003778]">{{ config('app.url') }}/<span id="url_preview">...</span></strong>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-to-br from-[#003778] to-[#0a4da1] hover:opacity-90 transition-all duration-200 shadow-[0_4px_12px_rgba(0,55,120,0.15)] group">
                        <span>Daftarkan Lembaga</span>
                        <span class="material-symbols-outlined ml-2 text-[20px] group-hover:translate-x-1 transition-transform">app_registration</span>
                    </button>
                    <p class="mt-4 text-center text-xs text-[#424752]">
                        Dengan mendaftar, Anda menyetujui Ketentuan Layanan kami.
                    </p>
                </div>
            </form>
        </div>
    </div>
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
