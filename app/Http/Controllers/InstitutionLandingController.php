<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class InstitutionLandingController extends Controller
{
    /**
     * Display the landing page for a specific institution.
     * If the user is already authenticated (as student or admin), redirect them directly.
     */
    public function index($subdomain)
    {
        $institution = Institution::where('subdomain', $subdomain)->firstOrFail();

        // If already logged in as a STUDENT → go to student dashboard
        if (Auth::guard('student')->check()) {
            return redirect()->route('institution.student.dashboard', $subdomain);
        }

        // If already logged in as ADMIN/STAFF/PROCTOR → go to admin dashboard
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('institution.landing', compact('institution'));
    }

    /**
     * Display the branded login page for the institution (Admin/Staff).
     */
    public function login($subdomain): View
    {
        // Ensure institution exists
        $institution = Institution::where('subdomain', $subdomain)->firstOrFail();
        
        // Return standard login view. AppServiceProvider will inject the branding.
        return view('auth.login');
    }
}
