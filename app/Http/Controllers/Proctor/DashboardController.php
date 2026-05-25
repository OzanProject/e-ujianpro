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

        // 1. Get active sessions assigned to this teacher
        $today = Carbon::today();
        
        $activeSessions = $user->proctorAssignments()
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
