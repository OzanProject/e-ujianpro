<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi | {{ $globalInstitution->name ?? 'E-Ujian PRO' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 bg-[url('https://s3.ap-southeast-1.amazonaws.com/cdn.e-ujian.com/static-assets/images/bg-auth.png')] bg-cover bg-center min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-inter">
    <div class="sm:mx-auto sm:w-full sm:max-w-lg">
        <div class="text-center">
             <img class="mx-auto h-20 w-auto object-contain drop-shadow-sm mb-6" src="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('img/logo-placeholder.png') }}" alt="Logo">
            <h2 class="text-center text-3xl font-extrabold text-gray-900 tracking-tight">
                Pendaftaran Akun Baru
            </h2>
             <p class="mt-2 text-center text-sm text-gray-600">
                Lengkapi data untuk bergabung dengan kami
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg px-4 sm:px-0">
        <div class="bg-white/90 backdrop-blur-xl py-8 px-6 shadow-2xl sm:rounded-3xl border border-white/60 sm:px-10 relative overflow-hidden">
             <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 rounded-full bg-indigo-400/10 blur-2xl"></div>
            
            <form class="space-y-6 relative z-10" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200" placeholder="Nama Lengkap Anda">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Role (Static for Institution Registration) -->
                <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-1">Mendaftar Sebagai</label>
                     <div class="flex items-center">
                        <span class="inline-flex items-center rounded-xl bg-blue-50 px-4 py-3 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 w-full justify-center shadow-sm">
                             <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                             </svg>
                             Admin Lembaga / Sekolah
                        </span>
                        <input type="hidden" name="role" value="admin_lembaga">
                     </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email</label>
                    <div class="relative">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200" placeholder="nama@sekolah.com">
                         @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 pr-10 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200" placeholder="Minimal 8 karakter">
                         @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                     <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200" placeholder="Ulangi password">
                    </div>
                </div>

                <!-- WhatsApp -->
                <div>
                     <label for="whatsapp" class="block text-sm font-semibold text-gray-700 mb-1">Nomor WhatsApp</label>
                    <div class="relative">
                        <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp') }}" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200" placeholder="08123456789">
                         @error('whatsapp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Institution Specific Fields -->
                <div id="institution_fields" class="space-y-6 border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-bold leading-6 text-gray-900">Detail Lembaga</h3>
                    
                    <!-- Institution Name -->
                    <div>
                        <label for="institution_name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lembaga</label>
                        <div class="relative">
                            <input id="institution_name" name="institution_name" type="text" value="{{ old('institution_name') }}" placeholder="Contoh: SMA Negeri 1 Jakarta" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200">
                             @error('institution_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                     <!-- Address -->
                     <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lembaga</label>
                        <div class="relative">
                            <textarea id="address" name="address" rows="2" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- City and Type -->
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                        <div>
                            <label for="city" class="block text-sm font-semibold text-gray-700 mb-1">Kabupaten/Kota</label>
                            <div class="relative">
                                <select id="city" name="city" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200">
                                    @include('auth.partials.city-options')
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Lembaga</label>
                            <div class="relative">
                                <select id="type" name="type" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200">
                                    <option value="">- Pilih -</option>
                                    <option value="SD/Sederajat">SD/Sederajat</option>
                                    <option value="SMP/Sederajat">SMP/Sederajat</option>
                                    <option value="SMA/Sederajat">SMA/Sederajat</option>
                                    <option value="Lembaga Bimbel">Lembaga Bimbel/Course</option>
                                    <option value="Yayasan">Yayasan</option>
                                    <option value="Organisasi">Organisasi</option>
                                    <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                                    <option value="Perorangan">Perorangan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Subdomain / URL -->
                    <div>
                        <label for="subdomain" class="block text-sm font-semibold text-gray-700 mb-1">Username Lembaga (URL)</label>
                        <div class="relative">
                            <input id="subdomain" name="subdomain" type="text" value="{{ old('subdomain') }}" placeholder="hanya-huruf-angka-tanpa-spasi" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200">
                            <p class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-200">
                                URL Sekolah Anda nanti: <br>
                                <span class="font-mono text-blue-600 font-medium">{{ config('app.url') }}/<span id="url_preview">...</span></span>
                            </p>
                             @error('subdomain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                     <!-- Affiliate -->
                    <div>
                        <label for="affiliate_code" class="block text-sm font-semibold text-gray-700 mb-1">Kode Afiliator (Opsional)</label>
                        <div class="relative">
                            <input id="affiliate_code" name="affiliate_code" type="text" value="{{ old('affiliate_code') }}" class="block w-full rounded-xl border-gray-300 bg-gray-50/50 py-3 px-4 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-200">
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="show_password" type="checkbox" onclick="showpswrd()" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 transition cursor-pointer">
                    <label for="show_password" class="ml-2 block text-sm text-gray-600 cursor-pointer">Tampilkan Password</label>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const subdomainInput = document.getElementById('subdomain');
                        const urlPreview = document.getElementById('url_preview');
                        
                        // Subdomain preview
                        subdomainInput.addEventListener('input', function() {
                            urlPreview.textContent = this.value;
                        });
                    });
                </script>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-3.5 text-sm font-bold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition duration-200 transform hover:scale-[1.01]">
                        Daftar Penyelenggara / Guru
                    </button>
                </div>
            </form>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-3 text-gray-500 rounded-full border border-gray-100 shadow-sm">Sudah punya akun?</span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">
                        Log in di sini
                    </a>
                </div>
            </div>
        </div>
        
         <p class="mt-8 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ $globalInstitution->name ?? 'E-Ujian PRO' }} <br>
            Protected by reCAPTCHA.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showpswrd() {
            var x = document.getElementById("password");
            var y = document.getElementById("password_confirmation");
            if (x.type === "password") {
                x.type = "text";
                y.type = "text";
            } else {
                x.type = "password";
                y.type = "password";
            }
        }

         // SweetAlert Notifications
        document.addEventListener('DOMContentLoaded', function () {
            // Success Message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            // Error Message
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                });
            @endif

            // Status Message
            @if(session('status'))
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: "{{ session('status') }}",
                });
            @endif

            // Validation Errors
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi Gagal',
                    html: '<ul class="text-left text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                });
            @endif
        });
    </script>
</body>
</html>
