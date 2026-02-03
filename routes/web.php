<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


// 1. Route Default / Halaman Utama
Route::get('/', function () {
    // Tampilan Landing Page untuk Aplikasi E-Ujian
    return view('welcome');
});

// 2. Route Dashboard Utama (dari Laravel Breeze, kita kostumisasi untuk Pengarahan Peran)
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Lakukan pengarahan (redirect) berdasarkan peran
    if ($user) {
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.super.dashboard');
        } elseif ($user->role === 'admin_lembaga') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'pengajar') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'peserta_ujian') {
            return redirect()->route('peserta.dashboard');
        } elseif ($user->role === 'operator') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'proctor') {
            // Fetch Institution Subdomain
            $adminId = $user->created_by;
            // Assuming Admin has Institution. 
            // We can query Institution directly by user_id
            $institution = \App\Models\Institution::where('user_id', $adminId)->first();
            $subdomain = $institution ? $institution->subdomain : 'default'; // Fallback if orphaned

            return redirect()->route('proctor.dashboard', ['subdomain' => $subdomain]);
        }
    }
    
    // Jika tidak ada peran spesifik (atau jika Breeze default dashboard digunakan)
    return view('dashboard');

})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Route Profile (DIPERTAHANKAN dari Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// 4. Import Route Otentikasi dan Admin

// Memuat route otentikasi (login, register, dll) dari Laravel Breeze
Route::get('/page/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

require __DIR__.'/auth.php';

// Route Super Admin
Route::prefix('admin/super')->name('admin.super.')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\SuperAdminController::class, 'dashboard'])->name('dashboard');
    // Manage Institutions
    Route::get('/institutions', [App\Http\Controllers\Admin\SuperAdminController::class, 'institutions'])->name('institutions.index');
    Route::get('/institutions/create', [App\Http\Controllers\Admin\SuperAdminController::class, 'create'])->name('institutions.create');
    Route::post('/institutions', [App\Http\Controllers\Admin\SuperAdminController::class, 'store'])->name('institutions.store');
    Route::post('/institutions/{id}/quota', [App\Http\Controllers\Admin\SuperAdminController::class, 'updateQuota'])->name('institutions.update_quota');
    Route::post('/institutions/{id}/approve', [App\Http\Controllers\Admin\SuperAdminController::class, 'approve'])->name('institutions.approve');
    Route::post('/institutions/{id}/suspend', [App\Http\Controllers\Admin\SuperAdminController::class, 'suspend'])->name('institutions.suspend');
    Route::post('/institutions/{id}/activate', [App\Http\Controllers\Admin\SuperAdminController::class, 'activate'])->name('institutions.activate');
    Route::delete('/institutions/{id}', [App\Http\Controllers\Admin\SuperAdminController::class, 'destroy'])->name('institutions.destroy');
    
    // Points Verification
    Route::get('/points', [App\Http\Controllers\Admin\SuperAdminPointController::class, 'index'])->name('points.index');
    Route::post('/points/{id}/approve', [App\Http\Controllers\Admin\SuperAdminPointController::class, 'approve'])->name('points.approve');
    Route::post('/points/{id}/reject', [App\Http\Controllers\Admin\SuperAdminPointController::class, 'reject'])->name('points.reject');

    // Platform Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SuperAdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SuperAdminSettingController::class, 'update'])->name('settings.update');

    // System Announcements
    Route::resource('announcements', App\Http\Controllers\Admin\SuperAdminAnnouncementController::class)->except(['create', 'edit', 'show']);
    Route::post('announcements/{id}/toggle', [App\Http\Controllers\Admin\SuperAdminAnnouncementController::class, 'toggleStatus'])->name('announcements.toggle');
});

// Memuat route untuk Panel Admin (Admin Lembaga dan Pengajar) dan Peserta
// Kita akan menggunakan prefix 'admin' untuk memuat routes/admin.php
// meskipun di dalamnya ada route untuk pengajar dan peserta
Route::prefix('admin')->group(function () {
    require __DIR__.'/admin.php';
});

// 5. Route for Student Area
Route::prefix('siswa')->name('student.')->group(function () {
    // Guest Routes
    Route::middleware('guest:student')->group(function () {
        Route::get('login', [App\Http\Controllers\Student\AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [App\Http\Controllers\Student\AuthController::class, 'login']);
    });

    // Authenticated Routes
    Route::middleware('auth:student')->group(function () {
        Route::post('logout', [App\Http\Controllers\Student\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');

        // Exam Routes
        Route::get('exam/{id}/confirmation', [App\Http\Controllers\Student\ExamController::class, 'confirmation'])->name('exam.confirmation');
        Route::post('exam/{id}/start', [App\Http\Controllers\Student\ExamController::class, 'start'])->name('exam.start');
        Route::get('exam/{id}/show', [App\Http\Controllers\Student\ExamController::class, 'show'])->name('exam.show');
        Route::post('exam/store-answer', [App\Http\Controllers\Student\ExamController::class, 'storeAnswer'])->name('exam.store_answer');
        Route::post('exam/{id}/finish', [App\Http\Controllers\Student\ExamController::class, 'finish'])->name('exam.finish');

        // History Routes
        Route::get('history', [App\Http\Controllers\Student\HistoryController::class, 'index'])->name('history.index');
        Route::get('history/{exam_session}', [App\Http\Controllers\Student\HistoryController::class, 'show'])->name('history.show');
    });
});

// 5a. Route for Student Area (Subdomain / Institution Specific)
// Matches: http://127.0.0.1:8000/smpn4kdp/siswa/dashboard
Route::prefix('{subdomain}/siswa')->name('institution.student.')->group(function () {
    // Guest Routes
    Route::middleware('guest:student')->group(function () {
        Route::get('login', [App\Http\Controllers\Student\AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [App\Http\Controllers\Student\AuthController::class, 'login']);
    });

    // Authenticated Routes
    Route::middleware('auth:student')->group(function () {
        Route::post('logout', [App\Http\Controllers\Student\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');

        // Exam Routes
        Route::get('exam/{id}/confirmation', [App\Http\Controllers\Student\ExamController::class, 'confirmation'])->name('exam.confirmation');
        Route::post('exam/{id}/start', [App\Http\Controllers\Student\ExamController::class, 'start'])->name('exam.start');
        Route::get('exam/{id}/show', [App\Http\Controllers\Student\ExamController::class, 'show'])->name('exam.show');
        Route::post('exam/store-answer', [App\Http\Controllers\Student\ExamController::class, 'storeAnswer'])->name('exam.store_answer');
        Route::post('exam/{id}/finish', [App\Http\Controllers\Student\ExamController::class, 'finish'])->name('exam.finish');

        // History Routes
        Route::get('history', [App\Http\Controllers\Student\HistoryController::class, 'index'])->name('history.index');
        Route::get('history/{exam_session}', [App\Http\Controllers\Student\HistoryController::class, 'show'])->name('history.show');
    });
});

// Portal / Choice Route
Route::get('/portal', function () {
    return view('portal');
})->name('portal');

// Institution Specific Routes (White Label)
Route::group(['prefix' => '{subdomain}'], function () {
    // Admin/Staff Login
    Route::get('/login', [App\Http\Controllers\InstitutionLandingController::class, 'login'])->name('institution.login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']); // Handle POST same as generic
    
    // Landing Page (Must be last in this group or handled carefully)
    Route::get('/', [App\Http\Controllers\InstitutionLandingController::class, 'index'])->name('institution.landing');
});

Route::group(['prefix' => '{subdomain}/proctor', 'middleware' => ['auth', 'role:proctor'], 'as' => 'proctor.'], function () {
    Route::get('/dashboard', [\App\Http\Controllers\Proctor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitor/{session}', [\App\Http\Controllers\Proctor\MonitorController::class, 'show'])->name('monitor.show');
    Route::get('/monitor/{session}/data', [\App\Http\Controllers\Proctor\MonitorController::class, 'getData'])->name('monitor.data');
    Route::post('/monitor/reset/{attempt}', [\App\Http\Controllers\Proctor\MonitorController::class, 'reset'])->name('monitor.reset');
    Route::post('/monitor/stop/{attempt}', [\App\Http\Controllers\Proctor\MonitorController::class, 'stop'])->name('monitor.stop');
});
