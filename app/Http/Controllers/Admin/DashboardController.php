<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // --- SUPER ADMIN DASHBOARD ---
        if ($user->role === 'super_admin') {
            // 1. Total Sekolah (Lembaga)
            $totalInstitutions = \App\Models\Institution::count();

            // 2. Total User (All Roles)
            $totalUsers = \App\Models\User::count();

            // 3. Total Siswa (Global)
            $totalStudents = \App\Models\Student::count();

            // 4. Ujian Aktif (Global)
            $activeExams = \App\Models\ExamSession::where('is_active', true)->count();

            // 5. Recent Institutions (Latest 5)
            $recentInstitutions = \App\Models\Institution::latest()->take(5)->get();

            // 6. Recent Users (Latest 5 Registered)
            $recentUsers = \App\Models\User::latest()->take(5)->get();

            return view('super_admin.dashboard', compact(
                'totalInstitutions',
                'totalUsers',
                'totalStudents', 
                'activeExams',
                'recentInstitutions',
                'recentUsers'
            ));
        }

        // --- ADMIN LEMBAGA / PENGAJAR DASHBOARD ---
        $userId = $user->id;
        // Determine Institution ID (Parent Admin)
        $institutionId = ($user->role == 'admin_lembaga') ? $userId : $user->created_by;

        // 1. Data Peserta (Siswa) - Scoped to Institution
        $pesertaCount = \App\Models\Student::count();

        // 2. Data Pengajar (Guru) - Filter by institution
        $guruCount = \App\Models\User::where('role', 'pengajar')
                                     ->where(function($q) use ($institutionId) {
                                         $q->where('id', $institutionId)
                                           ->orWhere('created_by', $institutionId);
                                     })
                                     ->count();

        // 3. Data Paket Soal - Scoped automatically via Multitenantable
        $paketSoalCount = \App\Models\ExamPackage::count();

        // 4. Sesi Ujian Aktif - Scoped automatically via Multitenantable
        $activeExamSessionCount = \App\Models\ExamSession::where('is_active', true)
                                ->count();

        // 5. Total Sesi (Optional)
        $totalExamSessionCount = \App\Models\ExamSession::count();

        // Get Max Students Quota (From Institution Admin)
        $institutionUser = ($user->role == 'admin_lembaga') ? $user : \App\Models\User::find($institutionId);
        $maxStudents = $institutionUser->max_students ?? 0;

        // 6. Statistics: Exam Attempts (Last 7 Days)
        $chartData = [];
        $chartLabels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $displayDate = now()->subDays($i)->format('d M');
            
            // Count attempts for this institution's students
            $count = \App\Models\ExamAttempt::whereDate('start_time', $date)
                ->count();
                
            $chartLabels[] = $displayDate;
            $chartData[] = $count;
        }

        // 7. System Announcements (Update Terakhir)
        $announcements = \App\Models\SystemAnnouncement::where('is_active', true)
                                                      ->latest()
                                                      ->take(3)
                                                      ->get();

        return view('admin.dashboard.index', compact(
            'pesertaCount', 
            'guruCount', 
            'paketSoalCount', 
            'activeExamSessionCount',
            'totalExamSessionCount',
            'chartLabels',
            'chartData',
            'announcements',
            'maxStudents'
        ));
    }
}
