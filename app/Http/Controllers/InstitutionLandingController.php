<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionLandingController extends Controller
{
    /**
     * Display the landing page for a specific institution.
     */
    public function index($subdomain): View
    {
        $institution = Institution::where('subdomain', $subdomain)->firstOrFail();

        // Share institution data globally for layout if needed, or just pass to view
        // Ideally the layout detects this context too.
        
        return view('institution.landing', compact('institution'));
    }
    /**
     * Display the branded login page for the institution.
     */
    public function login($subdomain): View
    {
        // Ensure institution exists
        $institution = Institution::where('subdomain', $subdomain)->firstOrFail();
        
        // Return standard login view. AppServiceProvider will inject the branding.
        return view('auth.login');
    }
}
