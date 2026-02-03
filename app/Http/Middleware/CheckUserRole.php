<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan pengguna terautentikasi
        if (!auth()->check()) {
            abort(403, 'Akses Dilarang. Silakan login terlebih dahulu.');
        }

        // Cek apakah peran pengguna ada dalam daftar peran yang diizinkan
        // Jika $roles kosong, berarti tidak ada pembatasan peran spesifik (opsional, tergantung logic)
        // Tapi di sini kita asumsikan middleware ini selalu dipanggil dengan parameter peran.
        if (!in_array(auth()->user()->role, $roles)) {
             abort(403, 'Akses Dilarang. Anda tidak memiliki hak akses (Role: ' . auth()->user()->role . ').');
        }

        return $next($request);
    }
}