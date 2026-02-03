<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - {{ $globalInstitution->name ?? 'E-Ujian PRO' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                     <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <img class="h-8 w-auto" src="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : 'https://s3.ap-southeast-1.amazonaws.com/cdn.e-ujian.com/static-assets/images/logo-v3-blue.png' }}" alt="Logo">
                        <span class="text-lg font-bold text-gray-900">{{ $globalInstitution->name ?? 'E-Ujian PRO' }}</span>
                    </a>
                </div>
                <div>
                     <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-blue-600 font-medium">&larr; Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-grow py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-8 pb-4 border-b border-gray-100">{{ $title }}</h1>
                
                <div class="prose max-w-none text-gray-600 leading-relaxed">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-sm text-gray-500">
        <div class="max-w-7xl mx-auto px-4">
             &copy; {{ date('Y') }} {{ $globalInstitution->name ?? 'E-Ujian PRO' }}. All rights reserved.
        </div>
    </footer>

</body>
</html>
