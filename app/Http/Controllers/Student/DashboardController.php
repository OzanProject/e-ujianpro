<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $now = now();

        // Determine Institution Scope via Admin (Creator of the Student)
        // Check if student has direct created_by or via user relation
        $adminId = $student->created_by ?? ($student->user ? $student->user->created_by : null);

        if (!$adminId) {
             // Fallback or Handle Orphan Student (Should not happen ideally)
             // Maybe return empty view or specific error, but let's try to proceed safely
             $validCreatorIds = [];
        } else { 

        // Get Valid Content Creators (Admin + Teachers created by Admin)
        $validCreatorIds = \App\Models\User::where('id', $adminId)
                                ->orWhere('created_by', $adminId)
                                ->pluck('id');
        } // Closing the else block

        // Fetch Institution Info for the student's school
        $institution = \App\Models\Institution::where('user_id', $adminId)->first();

        // Extract Student Level (e.g. "VII-A" -> "7")
        $parts = explode(' ', str_replace('-', ' ', $student->kelas));
        $first = strtoupper($parts[0] ?? '');
        $romans = [
            'TK' => 0, 'PAUD' => 0,
            'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6,
            'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10, 'XI' => 11, 'XII' => 12, 'XIII' => 13
        ];
        $studentLevel = null;
        if (isset($romans[$first])) {
            $studentLevel = (string)$romans[$first];
        } elseif (is_numeric($first)) {
            $studentLevel = (string)intval($first);
        }

        // Fetch valid exam sessions
        // 1. Is Active
        // 2. Start Time <= Now
        // 3. End Time >= Now
        // 4. Scope: Subject created by valid creators (Institution/Admin)
        // 5. Target Kelas matches or is empty
        $examSessions = ExamSession::with(['subject', 'examPackage'])
                            ->where('is_active', true)
                            ->where('start_time', '<=', $now)
                            ->where('end_time', '>=', $now)
                            ->whereHas('subject', function ($query) use ($validCreatorIds) {
                                $query->whereIn('created_by', $validCreatorIds);
                            })
                            ->where(function ($q) use ($studentLevel) {
                                $q->whereNull('target_kelas')
                                  ->orWhere('target_kelas', '');
                                if ($studentLevel !== null) {
                                    $q->orWhere('target_kelas', $studentLevel);
                                }
                            })
                            ->orderBy('start_time', 'asc')
                            ->get();

        // Check each session for existing attempts by this student
        $examSessions->transform(function ($session) use ($studentId) {
            $attempt = ExamAttempt::where('exam_session_id', $session->id)
                                  ->where('student_id', $studentId)
                                  ->first();

            $session->attempt_status = $attempt ? $attempt->status : 'not_started'; // 'not_started', 'in_progress', 'finished'
            $session->attempt_id = $attempt ? $attempt->id : null;
            
            return $session;
        });

        // Module 60 Reverted.
        // Module 14 Enhancement: Get Upcoming Exams (Jadwal Ujian)
        $upcomingSessions = ExamSession::with(['subject', 'examPackage'])
                            ->where('start_time', '>', $now)
                            ->whereHas('subject', function ($query) use ($validCreatorIds) {
                                $query->whereIn('created_by', $validCreatorIds);
                            })
                            ->where(function ($q) use ($studentLevel) {
                                $q->whereNull('target_kelas')
                                  ->orWhere('target_kelas', '');
                                if ($studentLevel !== null) {
                                    $q->orWhere('target_kelas', $studentLevel);
                                }
                            })
                            ->orderBy('start_time', 'asc')
                            ->take(6) // Limit to next 6 exams
                            ->get();
        
        // Also transform status for upcoming (though usually not started)
        $upcomingSessions->transform(function ($session) use ($studentId) {
             // Logic similar to above, mostly checking if attempting early is possible (unlikely)
             // Just pass basic info
             return $session;
        });

        return view('student.dashboard.index', compact('examSessions', 'upcomingSessions', 'institution'));
    }
}
