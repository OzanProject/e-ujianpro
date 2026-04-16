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
        if (!in_array(auth()->user()->role, $roles)) {
             return redirect()->route('dashboard')->with('error', 'Akses Dilarang. Anda tidak memiliki hak akses (Role: ' . auth()->user()->role . ') untuk menu tersebut.');
        }

        return $next($request);
    }
}