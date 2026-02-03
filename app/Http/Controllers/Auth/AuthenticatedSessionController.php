<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::user()->status !== 'active') {
            $status = Auth::user()->status;
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = ($status === 'pending') 
                ? 'Akun Anda masih dalam proses verifikasi. Silakan hubungi Admin.' 
                : 'Akun Anda telah disuspend. Silakan hubungi Admin.';

            return redirect()->back()->withErrors(['email' => $message]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        $redirectUrl = '/';

        if ($user && $user->role === 'admin_lembaga') {
            $institution = \App\Models\Institution::where('user_id', $user->id)->first();
            if ($institution && $institution->subdomain) {
                // Redirect to institution landing page (e.g. /smpn4kdp)
                // This handles both path-based (local) and subdomain logic if route is set up correctly
                $redirectUrl = route('institution.landing', $institution->subdomain);
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect($redirectUrl);
    }
}
