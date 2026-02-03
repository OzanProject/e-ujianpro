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

        // 1. Data Peserta (Siswa) - Scoped to Institution (Created By)
        // Note: 'user_id' in students table usually refers to the student's own user account.
        // We should use 'created_by' (which stores the Admin's ID) or filter by relation if needed.
        // Assuming 'created_by' was added for this exact purpose in Module 300.
        $pesertaCount = \App\Models\Student::where('created_by', $institutionId)->count();

        // 2. Data Pengajar (Guru) - Scoped to Institution
        $guruCount = \App\Models\User::where('role', 'pengajar')
                                     ->where('created_by', $institutionId)
                                     ->count();

        // 3. Data Paket Soal - Scoped to User (Personal Work)
        // If Admin, sees their own. If Teacher, sees their own.
        $paketSoalCount = \App\Models\ExamPackage::where('created_by', $userId)->count();

        // 4. Sesi Ujian Aktif
        // Filter sessions where the related package belongs to this user
        $activeExamSessionCount = \App\Models\ExamSession::whereHas('examPackage', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })->where('is_active', true)->count();

        // 5. Total Sesi (Optional)
        $totalExamSessionCount = \App\Models\ExamSession::whereHas('examPackage', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })->count();

        // Get Max Students Quota (From Institution Admin)
        $institutionUser = ($user->role == 'admin_lembaga') ? $user : \App\Models\User::find($institutionId);
        $maxStudents = $institutionUser->max_students ?? 0;

        // 6. Statistics: Exam Attempts (Last 7 Days)
        // We filter attempts for students belonging to this institution
        $chartData = [];
        $chartLabels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $displayDate = now()->subDays($i)->format('d M');
            
            // Count attempts started on this date by students of this Institution
            // But usually we want to see attempts on *My Exams*.
            // Let's filter by Exam Packages created by Me ($userId).
            $count = \App\Models\ExamAttempt::whereDate('start_time', $date)
                ->whereHas('examSession.examPackage', function($q) use ($userId) {
                    $q->where('created_by', $userId);
                })
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
