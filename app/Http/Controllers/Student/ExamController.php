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
             $questions = $session->examPackage->questions()->with('options')->get();
        } else {
             $questions = \App\Models\Question::where('subject_id', $session->subject_id)->with('options')->get();
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

        // Calculate Score Immediate
        // Calculate Score with Conversion Logic
        $answers = ExamAnswer::where('exam_attempt_id', $attempt->id)->get();
        
        // Group answers by question_group_id
        // We need to fetch questions to know their group
        if ($session->exam_package_id) {
            $questions = $session->examPackage->questions()
                            ->with('questionGroup') // Ensure we know the group
                            ->get()
                            ->keyBy('id');
        } else {
            $questions = \App\Models\Question::where('subject_id', $session->subject_id)
                            ->with('questionGroup')
                            ->get()
                            ->keyBy('id');
        }

        $groups = [];
        $totalCorrect = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $q) {
            $groupId = $q->question_group_id ?? 'default';
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = ['total' => 0, 'correct' => 0];
            }
            $groups[$groupId]['total']++;
        }

        foreach ($answers as $ans) {
            $q = $questions[$ans->question_id] ?? null;
            if ($q) {
                $groupId = $q->question_group_id ?? 'default';
                
                $opt = \App\Models\QuestionOption::find($ans->question_option_id);
                
                // Re-verify and update Is Correct status in DB (Syncing)
                $isCorrect = $opt && $opt->is_correct;
                if ($ans->is_correct != $isCorrect) {
                     $ans->is_correct = $isCorrect;
                     $ans->save();
                }

                if ($isCorrect) {
                    $groups[$groupId]['correct']++;
                    $totalCorrect++;
                }
            }
        }

        $finalScore = 0;
        $activeScalesCount = 0;

        foreach ($groups as $groupId => $stats) {
            if ($groupId === 'default') {
                continue; // We handle valid groups first
            }

            // Check for Scale
            // Use Student's Creator (Admin Lembaga) to find Institution
            // This ensures we get the correct Institution even if ExamSession was created by a Teacher
            $student = Auth::guard('student')->user();
            $institutionId = $student && $student->user_id ? \App\Models\Institution::where('user_id', $student->user_id)->value('id') : null;
            
            // Fallback to session owner if student has no creator (unlikely but safe)
            if (!$institutionId && $session->user_id) {
                 $institutionId = \App\Models\Institution::where('user_id', $session->user_id)->value('id');
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
            } else {
                // Determine weight? 
                // If mixed mode (Some scaled, some not), this is tricky.
                // Assumption: If using scales, expected behavior is "Points Accumulation".
                // If no scale defined for this group, add 0? Or add proportional?
                // Let's add (correct/total_in_group)*100 ?? No that makes 100+100.
                // Let's assume if Scalling is active, ALL groups should be scaled.
                // If not scaled, we add (correct / total_exam) * 100 ? No impossible to mix.
                
                // Fallback: If partial scaling, we treat non-scaled groups as "0 weight" or raw points?
                // Let's stick to: If ANY scale found, use Accumulation.
                // If NO scale found (activeScalesCount == 0), use global linear.
            }
        }

        // Handle Default Group or Non-Scaled Groups
        if ($activeScalesCount > 0) {
            // Grading Mode: Custom Scale
            // We only sum the SCALED scores.
            // Any group without a scale contributes 0 to the score? 
            // Or maybe we should try to find a scale for them too.
            // If no scale, we leave it as 0 (Teacher forgot to define scale).
        } else {
            // Standard Mode: Linear
            $finalScore = $totalQuestions > 0 ? ($totalCorrect / $totalQuestions) * 100 : 0;
        }
        
        $attempt->score = $finalScore;
        $attempt->save();

        if ($request->route('subdomain')) {
            return redirect()->route('institution.student.dashboard', $request->route('subdomain'))
                             ->with('success', 'Ujian telah selesai. Nilai Anda: ' . number_format($finalScore, 2));
        }
        return redirect()->route('student.dashboard')->with('success', 'Ujian telah selesai. Nilai Anda: ' . number_format($finalScore, 2));
    }
}
