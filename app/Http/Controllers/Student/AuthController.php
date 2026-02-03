<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = ['nis' => $request->nis, 'password' => $request->password];

        // START: Fix for Scoped NIS (Subdomain Login)
        // Since NIS is not unique globally, we must filter by Institution (created_by)
        if ($subdomain = $request->route('subdomain')) {
            $institution = \App\Models\Institution::where('subdomain', $subdomain)->first();
            if ($institution) {
                // Students are created by the Institution Admin (user_id)
                $credentials['created_by'] = $institution->user_id;
            }
        }
        // END: Fix for Scoped NIS

        if (Auth::guard('student')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Redirect to Subdomain Dashboard if login was from subdomain
            if ($request->route('subdomain')) {
                // Force redirect to dashboard to prevent implicit 'intended' redirect to main domain
                return redirect()->route('institution.student.dashboard', $request->route('subdomain'));
            }

            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'nis' => 'NIS atau Password salah (Pastikan Anda login di sekolah yang benar).',
        ])->onlyInput('nis');
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to Subdomain Login if logout was from subdomain
        if ($request->route('subdomain')) {
            return redirect()->route('institution.student.login', $request->route('subdomain'));
        }

        return redirect()->route('student.login');
    }
}
