<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFour();

        // Share Institution Data to all views
        // Share Institution Data to all views dynamically
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $globalInstitution = null;

            // Fetch Platform Logo
            $platformLogo = \App\Models\Setting::getValue('app_logo');

            // 1. Check for Subdomain in Route (Highest Priority for Branding)
            $subdomain = request()->route('subdomain');
            if ($subdomain) {
                $institutionBySubdomain = \App\Models\Institution::where('subdomain', $subdomain)->first();
                if ($institutionBySubdomain) {
                    $globalInstitution = $institutionBySubdomain;
                }
            }

            // 2. Fallback to Auth User's Institution if not set by subdomain
            if (!$globalInstitution && $user) {
                if ($user->role === 'super_admin') {
                    // For Super Admin, use Platform Settings
                    $globalInstitution = (object) [
                        'name' => \App\Models\Setting::getValue('app_name', 'E-Ujian PRO'),
                        'logo' => $platformLogo,
                    ];
                } elseif (in_array($user->role, ['admin_lembaga', 'pengajar', 'operator', 'siswa'])) {
                    // For others, try to find their linked institution
                    if (method_exists($user, 'institution') && $user->institution) {
                         $globalInstitution = $user->institution;
                    } elseif ($user->role === 'admin_lembaga') {
                         $globalInstitution = \App\Models\Institution::where('user_id', $user->id)->first();
                    }
                    // For Student, we might need a more complex lookup if not directly linked, 
                    // but usually the subdomain should handle the branding needs.
                }
            }
            
            // 3. Final Fallback for guests or if nothing found
            if (!$globalInstitution) {
                $globalInstitution = (object) [
                    'name' => \App\Models\Setting::getValue('app_name', 'E-Ujian PRO'),
                    'logo' => $platformLogo,
                ];
            }

            // LOGO FALLBACK LOGIC:
            // If Institution exists but has NO logo, use Platform Logo
            if ($globalInstitution && empty($globalInstitution->logo)) {
                $globalInstitution->logo = $platformLogo;
            }

            $view->with('globalInstitution', $globalInstitution);
        });
    }
}
