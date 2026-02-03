<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecapController extends Controller
{

    public function examResult(Request $request)
    {
        $user = Auth::user();
        
        // Scope Exam Sessions
        if ($user->role === 'pengajar') {
            $examSessions = ExamSession::whereIn('subject_id', $user->subjects->pluck('id'))
                                ->with(['subject', 'examPackage'])
                                ->orderByDesc('start_time')
                                ->get();
        } else {
            // Admin Lembaga & Operator scopes by Subject ownership
            // If Admin Lembaga, view own subjects. If Operator, view parent's subjects.
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
            $examSessions = ExamSession::whereIn('subject_id', $subjects)
                                ->with(['subject', 'examPackage'])
                                ->orderByDesc('start_time')
                                ->get();
        }

        $selectedSession = null;
        $attempts = collect([]);
        $summary = [
            'total_students' => 0,
            'avg_score' => 0,
            'max_score' => 0,
            'min_score' => 0,
            'passed' => 0,
            'failed' => 0
        ];

        if ($request->has('exam_session_id') && $request->exam_session_id) {
            // Verify ownership
            $sessionCheck = $examSessions->where('id', $request->exam_session_id)->first();
            
            if ($sessionCheck) {
                $selectedSession = ExamSession::with('examPackage')->find($request->exam_session_id);
                
                $attempts = $selectedSession->attempts()
                    ->with(['student.group', 'student', 'answers.option', 'answers.question.options']) // Optimization for verification
                    ->orderByDesc('score')
                    ->get();

                // Calculate Summary
                if ($attempts->count() > 0) {
                    $summary['total_students'] = $attempts->count();
                    $summary['avg_score'] = $attempts->avg('score');
                    $summary['max_score'] = $attempts->max('score');
                    $summary['min_score'] = $attempts->min('score');
                    $summary['passed'] = $attempts->where('score', '>=', 75)->count(); // Assuming KKM 75 for now, or dynamic?
                    $summary['failed'] = $attempts->where('score', '<', 75)->count();
                }
            }
        }

        return view('admin.recap.exam_result', compact('examSessions', 'selectedSession', 'attempts', 'summary'));
    }

    public function printExamResult(Request $request)
    {
        $selectedSession = ExamSession::with(['examPackage.subject'])->find($request->exam_session_id);
        $attempts = collect([]);

        if ($selectedSession) {
             $attempts = $selectedSession->attempts()
                ->with('student.group', 'student', 'answers')
                ->orderByDesc('score')
                ->get();
        }

        $institution = \App\Models\Institution::first();

        return view('admin.recap.print_exam_result', compact('selectedSession', 'attempts', 'institution'));
    }
}
