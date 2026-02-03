<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckUserRole; // <-- Import class middleware

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            // \App\Http\Middleware\EncryptCookies::class, // Contoh middleware web
        ]);

        // Tambahkan alias middleware di sini (middleware yang dipanggil di route)
        $middleware->alias([
            // ... middleware alias yang sudah ada (ex: 'auth', 'verified', dll.)
            'role' => CheckUserRole::class, // <-- Tambahkan alias ini
        ]);

        // Smart Redirection for Unauthenticated Users (Guests)
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            // 1. If Subdomain Context (via Route Param)
            if ($subdomain = $request->route('subdomain')) {
                // If accessing Student Area -> Student Login
                if ($request->routeIs('institution.student.*') || $request->is('*/siswa/*')) {
                     return route('institution.student.login', $subdomain);
                }
                // Default Institution Login
                return route('institution.login', $subdomain);
            }

            // 2. If Global Context
            // Student Area
            if ($request->is('siswa') || $request->is('siswa/*') || $request->routeIs('student.*')) {
                return route('student.login');
            }

            // Default Admin/Global Login
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();