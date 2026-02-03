<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Show confirmation page before starting/resuming exam
     */
    public function confirmation($p1, $p2 = null)
    {
        $id = $p2 ?? $p1;
        $session = ExamSession::with(['subject', 'examPackage'])->findOrFail($id);
        
        // Validation Checks
        if (!$session->is_active) {
            $route = request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard');
            return redirect($route)->with('error', 'Ujian ini tidak aktif.');
        }

        $now = now();
        if ($now < $session->start_time || $now > $session->end_time) {
            $route = request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard');
            return redirect($route)->with('error', 'Waktu ujian belum mulai atau sudah berakhir.');
        }

        $studentId = Auth::guard('student')->id();
        $attempt = ExamAttempt::where('exam_session_id', $id)
                    ->where('student_id', $studentId)
                    ->first();

        if ($attempt && $attempt->status == 'completed') {
            $route = request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard');
            return redirect($route)->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        return view('student.exam.confirmation', compact('session', 'attempt'));
    }

    /**
     * Start or Resume the exam
     */
    public function start(Request $request, $p1, $p2 = null)
    {
        $id = $p2 ?? $p1;
        $session = ExamSession::findOrFail($id);
        $studentId = Auth::guard('student')->id();

        // Check if attempting to resume or start new
        $existingAttempt = ExamAttempt::where('exam_session_id', $id)
                            ->where('student_id', $studentId)
                            ->first();

        // If no existing attempt, Validate Token
        if (!$existingAttempt) {
            $request->validate([
                'token' => 'required|string'
            ]);

            if (strtoupper($request->token) !== strtoupper($session->token)) {
                 $route = request()->route('subdomain') ? route('institution.student.exam.confirmation', ['subdomain' => request()->route('subdomain'), 'id' => $session->id]) : route('student.exam.confirmation', $session->id);
                 return redirect($route)->with('error', 'Token salah! Silakan coba lagi.');
            }
        }

        // Check/Create Attempt
        $attempt = ExamAttempt::firstOrCreate(
            [
                'exam_session_id' => $id,
                'student_id' => $studentId,
            ],
            [
                'start_time' => now(),
                'status' => 'in_progress'
            ]
        );

        if ($attempt->status == 'completed') {
             $route = request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard');
             return redirect($route)->with('error', 'Ujian sudah selesai.');
        }

        if ($request->route('subdomain')) {
            return redirect()->route('institution.student.exam.show', ['subdomain' => $request->route('subdomain'), 'id' => $session->id]);
        }
        return redirect()->route('student.exam.show', $session->id);
    }

    /**
     * The Main Exam Interface
     */
    public function show($p1, $p2 = null)
    {
        $id = $p2 ?? $p1;
        $session = ExamSession::findOrFail($id);
        $studentId = Auth::guard('student')->id();
        
        $attempt = ExamAttempt::where('exam_session_id', $id)
                    ->where('student_id', $studentId)
                    ->firstOrFail();

        if ($attempt->status == 'completed') {
             $route = request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard');
             return redirect($route)->with('error', 'Ujian sudah selesai.');
        }

        // Fetch questions properly ordered
        // If package is set, use package questions. Otherwise use all subject questions.
        if ($session->exam_package_id) {
             $questions = $session->examPackage->questions()->with(['options', 'readingText'])->get();
        } else {
             $questions = \App\Models\Question::where('subject_id', $session->subject_id)->with(['options', 'readingText'])->get();
        }

        // Calculate Remaining Time
        // Duration is in minutes.
        // End time is strictly SESSION END TIME or START TIME + DURATION?
        // Usually: Least of (Session End Time) OR (Attempt Start + Duration)
        
        $sessionEndTime = $session->end_time;
        $attemptEndTime = $attempt->start_time->copy()->addMinutes($session->duration);
        
        $finalEndTime = $sessionEndTime < $attemptEndTime ? $sessionEndTime : $attemptEndTime;
        
        $remainingSeconds = now()->diffInSeconds($finalEndTime, false);

        if ($remainingSeconds <= 0) {
             // Auto finish if time is up (should act trigger finish)
             $route = request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard');
             return redirect($route)->with('error', 'Waktu habis.');
        }

        // Fetch saved answers
        $savedAnswers = ExamAnswer::where('exam_attempt_id', $attempt->id)
                            ->pluck('question_option_id', 'question_id')
                            ->toArray();

        return view('student.exam.show', compact('session', 'questions', 'attempt', 'remainingSeconds', 'savedAnswers'));
    }

    public function storeAnswer(Request $request)
    {
        $request->validate([
            'question_id'     => 'required',
            'option_id'       => 'nullable',
        ]);

        $studentId = Auth::guard('student')->id();

        // 1. Get Attempt ID from session or request (It's safer to get active exam session)
        // Ideally we should pass session_id or attempt_id. 
        // Let's assume student can only have ONE active attempt per session. or pass attempt from view.
        // For simplicity let's require session_id in request.
        
        if (!$request->exam_session_id) {
             return response()->json(['status' => 'error', 'message' => 'Session ID required'], 400);
        }

        $attempt = ExamAttempt::where('exam_session_id', $request->exam_session_id)
                    ->where('student_id', $studentId)
                    ->firstOrFail();
        
        if($attempt->status == 'completed') {
             return response()->json(['status' => 'error', 'message' => 'Ujian sudah selesai.'], 403);
        }

        // Fetch the option to check correctness
        $option = \App\Models\QuestionOption::find($request->option_id);
        $isCorrect = $option ? $option->is_correct : false;

        // 2. Save/Update Answer
        ExamAnswer::updateOrCreate(
            [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'question_option_id' => $request->option_id,
                'is_doubtful' => $request->is_doubtful ?? false,
                'is_correct' => $isCorrect 
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function finish(Request $request, $p1, $p2 = null)
    {
        $id = $p2 ?? $p1;
        $session = ExamSession::findOrFail($id);
        $studentId = Auth::guard('student')->id();
        
        $attempt = ExamAttempt::where('exam_session_id', $id)
                    ->where('student_id', $studentId)
                    ->firstOrFail();

        $attempt->status = 'completed';
        $attempt->end_time = now();
        $attempt->save();

        // 1. Fetch Questions & Answers
        $questions = $this->getQuestionsForSession($session);
        $answers = ExamAnswer::where('exam_attempt_id', $attempt->id)->get();

        // 2. Calculate Stats per Group
        $groups = $this->calculateGroupStats($questions, $answers);

        // 3. Calculate Final Score (Linear or Scaled)
        $finalScore = $this->calculateFinalScore($groups, $questions->count(), $session);

        $attempt->score = $finalScore;
        $attempt->save();

        $message = 'Ujian telah selesai. Nilai Anda: ' . number_format($finalScore, 2);

        if ($request->route('subdomain')) {
            return redirect()->route('institution.student.dashboard', $request->route('subdomain'))
                             ->with('success', $message);
        }
        return redirect()->route('student.dashboard')->with('success', $message);
    }

    private function getQuestionsForSession($session)
    {
        if ($session->exam_package_id) {
            return $session->examPackage->questions()
                            ->with('questionGroup')
                            ->get()
                            ->keyBy('id');
        } else {
            return \App\Models\Question::where('subject_id', $session->subject_id)
                            ->with('questionGroup')
                            ->get()
                            ->keyBy('id');
        }
    }

    private function calculateGroupStats($questions, $answers)
    {
        $groups = [];

        // Initialize groups
        foreach ($questions as $q) {
            $groupId = $q->question_group_id ?? 'default';
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = ['total' => 0, 'correct' => 0];
            }
            $groups[$groupId]['total']++;
        }

        // Process Answers
        foreach ($answers as $ans) {
            /** @var \App\Models\ExamAnswer $ans */
            $q = $questions[$ans->question_id] ?? null;
            if ($q) {
                $groupId = $q->question_group_id ?? 'default';
                
                $opt = \App\Models\QuestionOption::find($ans->question_option_id);
                $isCorrect = $opt && $opt->is_correct;

                // Sync correctness if changed
                if ($ans->is_correct != $isCorrect) {
                     $ans->is_correct = $isCorrect;
                     $ans->save();
                }

                if ($isCorrect) {
                    $groups[$groupId]['correct']++;
                }
            }
        }
        return $groups;
    }

    private function calculateFinalScore($groups, $totalQuestions, $session)
    {
        $finalScore = 0;
        $activeScalesCount = 0;
        $totalCorrectGlobal = 0;

        // Try to determine Institution ID for Scaling
        $student = Auth::guard('student')->user();
        $institutionId = $student && $student->user_id ? \App\Models\Institution::where('user_id', $student->user_id)->value('id') : null;
        
        if (!$institutionId && $session->subject && $session->subject->created_by) {
             $institutionId = \App\Models\Institution::where('user_id', $session->subject->created_by)->value('id');
        }

        foreach ($groups as $groupId => $stats) {
            $totalCorrectGlobal += $stats['correct'];

            if ($groupId === 'default') {
                continue;
            }

            $scale = null;
            if ($institutionId) {
                $scale = \App\Models\ScoreScale::where('institution_id', $institutionId)
                            ->where('question_group_id', $groupId)
                            ->where('correct_count', $stats['correct'])
                            ->first();
            }

            if ($scale) {
                $finalScore += $scale->scaled_score;
                $activeScalesCount++;
            }
        }

        if ($activeScalesCount > 0) {
            // Scaled Mode
            return $finalScore;
        } else {
            // Linear Mode
            return $totalQuestions > 0 ? ($totalCorrectGlobal / $totalQuestions) * 100 : 0;
        }
    }
}
