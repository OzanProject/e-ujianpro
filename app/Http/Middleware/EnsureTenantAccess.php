<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $request->route('subdomain');

        // Jika tidak ada parameter subdomain di route, abaikan pengecekan ini
        if (!$subdomain) {
            return $next($request);
        }

        // 1. Cari institusi berdasarkan subdomain di URL
        $institution = Institution::where('subdomain', $subdomain)->first();

        if (!$institution) {
            abort(404, 'Lembaga tidak ditemukan.');
        }

        // 2. Jika user login (Guard Web - Admin/Guru)
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            // Bypass untuk Super Admin
            if ($user->role === 'super_admin') {
                return $next($request);
            }

            // Cek kepemilikan: 
            // - Apakah dia admin pemilik institusi?
            // - Atau apakah dia staf/guru (created_by) dari admin pemilik institusi tersebut?
            $isOwner = ($institution->user_id == $user->id);
            $isStaff = ($institution->user_id == $user->created_by);

            if (!$isOwner && !$isStaff) {
                abort(403, 'Anda tidak memiliki hak akses untuk lembaga ini.');
            }
        }

        // 3. Jika user login (Guard Student - Siswa)
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();

            // Siswa terikat ke institusi melalui field 'created_by' (ID Admin)
            if ($institution->user_id != $student->created_by) {
                // Berikan pesan error yang jelas jika salah kamar
                abort(403, 'Akun Anda tidak terdaftar di lembaga ini. Pastikan Anda masuk lewat portal yang benar.');
            }
        }

        return $next($request);
    }
}
