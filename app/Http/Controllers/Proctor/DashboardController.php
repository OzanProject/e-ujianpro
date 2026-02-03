<?php

namespace App\Http\Controllers\Proctor;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index($subdomain)
    {
        $user = auth()->user();
        
        // Data Scoping:
        // Proctor is created by an Admin (admin_lembaga).
        // Exceptions: If created_by is null (legacy/error), we might show nothing or all (security risk).
        // We assume created_by is valid.
        
        $adminId = $user->created_by;

        if (!$adminId) {
             // Fallback/Safety: If no creator, show nothing.
             return view('proctor.dashboard', ['activeSessions' => collect([])]);
        }

        // 1. Get all Users in this Institution (Admin + Teachers)
        // Teachers are created by the same Admin.
        $institutionUserIds = \App\Models\User::where('created_by', $adminId)
                                ->orWhere('id', $adminId)
                                ->pluck('id');

        // 2. Get Subjects created by these users
        // This assumes Subjects are owned by creators. 
        // Note: Sometimes subjects are linked via Pivot table (subject_user).
        // Let's try to get subjects from Relation or pivot if possible, but standard logic is ownership.
        // Based on Subject model, it has `created_by`.
        
        $subjectIds = \App\Models\Subject::whereIn('created_by', $institutionUserIds)->pluck('id');

        // 3. Get Active Sessions for these subjects
        $today = Carbon::today();
        
        $activeSessions = ExamSession::whereIn('subject_id', $subjectIds)
                            ->where(function($q) use ($today) {
                                $q->whereDate('start_time', $today)
                                  ->orWhere(function($query) {
                                      $now = Carbon::now();
                                      $query->where('start_time', '<=', $now)
                                            ->where('end_time', '>=', $now);
                                  });
                            })
                            ->orderBy('start_time', 'asc')
                            ->with('subject')
                            ->get();

        return view('proctor.dashboard', compact('activeSessions'));
    }
}
