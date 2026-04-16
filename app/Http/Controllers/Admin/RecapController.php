<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecapController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.recap' : 'admin.recap';
    }

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
            $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
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
                    ->with(['student.group', 'student', 'answers.option', 'answers.question.options']) 
                    ->orderByDesc('score')
                    ->get();

                if ($attempts->count() > 0) {
                    $summary['total_students'] = $attempts->count();
                    $summary['avg_score'] = $attempts->avg('score');
                    $summary['max_score'] = $attempts->max('score');
                    $summary['min_score'] = $attempts->min('score');
                    $summary['passed'] = $attempts->where('score', '>=', 75)->count(); 
                    $summary['failed'] = $attempts->where('score', '<', 75)->count();
                }
            }
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.recap.exam_result', compact('examSessions', 'selectedSession', 'attempts', 'summary', 'baseRoute'));
    }

    public function printExamResult(Request $request)
    {
        $user = Auth::user();
        $selectedSession = ExamSession::with(['examPackage.subject', 'examPackage.subject.creator'])->find($request->exam_session_id);

        if (!$selectedSession) {
            abort(404);
        }

        $hasAccess = false;
        if ($user->role === 'pengajar') {
             $allowedSubjectIds = $user->subjects->pluck('id')->toArray();
             if (in_array($selectedSession->subject_id, $allowedSubjectIds)) {
                 $hasAccess = true;
             }
        } else {
             $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
             if ($selectedSession->subject->created_by == $creatorId) {
                 $hasAccess = true;
             }
        }

        if (!$hasAccess && $user->role !== 'super_admin') {
            abort(403, 'Unauthorized access.');
        }

        $attempts = $selectedSession->attempts()
            ->with('student.group', 'student', 'answers')
            ->orderByDesc('score')
            ->get();

        $subjectOwnerId = $selectedSession->subject->created_by;
        $institution = \App\Models\Institution::where('user_id', $subjectOwnerId)->first();

        if (!$institution) {
             $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
             $institution = \App\Models\Institution::where('user_id', $creatorId)->first();
        }

        return view('admin.recap.print_exam_result', compact('selectedSession', 'attempts', 'institution'));
    }
}
