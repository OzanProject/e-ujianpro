<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    /**
     * Show confirmation page before starting/resuming exam.
     */
    public function confirmation($p1, $p2 = null)
    {
        $id = $p2 ?? $p1;
        $session = $this->getValidSession($id, ['subject', 'examPackage']);

        if (!$session->is_active) {
            return redirect($this->dashboardRoute())
                ->with('error', 'Ujian ini tidak aktif.');
        }

        $now = now();
        if ($now < $session->start_time || $now > $session->end_time) {
            return redirect($this->dashboardRoute())
                ->with('error', 'Waktu ujian belum mulai atau sudah berakhir.');
        }

        $studentId = Auth::guard('student')->id();

        $attempt = ExamAttempt::where('exam_session_id', $id)
            ->where('student_id', $studentId)
            ->first();

        if ($attempt && $attempt->status === 'in_progress') {
            $attemptEndTime = $attempt->start_time->copy()->addMinutes($session->duration);

            if (now() > $attemptEndTime || now() > $session->end_time) {
                $this->autoFinish($attempt, $session);
                $attempt->refresh();
            }
        }

        if ($attempt && $attempt->status === 'completed') {
            return redirect($this->dashboardRoute())
                ->with('success', 'Ujian ini sudah selesai.');
        }

        return view('student.exam.confirmation', compact('session', 'attempt'));
    }

    /**
     * Start or resume the exam.
     */
    public function start(Request $request, $p1, $p2 = null)
    {
        $id = $p2 ?? $p1;
        $session = $this->getValidSession($id);
        $studentId = Auth::guard('student')->id();

        $existingAttempt = ExamAttempt::where('exam_session_id', $id)
            ->where('student_id', $studentId)
            ->first();

        if (!$existingAttempt) {
            $request->validate([
                'token' => 'required|string',
            ]);

            if (strtoupper((string) $request->token) !== strtoupper((string) $session->token)) {
                return redirect($this->confirmationRoute($session->id))
                    ->with('error', 'Token salah! Silakan coba lagi.');
            }
        }

        $attempt = ExamAttempt::firstOrCreate(
            [
                'exam_session_id' => $id,
                'student_id' => $studentId,
            ],
            [
                'start_time' => now(),
                'status' => 'in_progress',
            ]
        );

        if ($attempt->status === 'in_progress') {
            $attemptEndTime = $attempt->start_time->copy()->addMinutes($session->duration);

            if (now() > $attemptEndTime || now() > $session->end_time) {
                $this->autoFinish($attempt, $session);
                $attempt->refresh();
            }
        }

        if ($attempt->status === 'completed') {
            return redirect($this->dashboardRoute())
                ->with('error', 'Ujian sudah selesai.');
        }

        if ($request->route('subdomain')) {
            return redirect()->route('institution.student.exam.show', [
                'subdomain' => $request->route('subdomain'),
                'id' => $session->id,
            ]);
        }

        return redirect()->route('student.exam.show', $session->id);
    }

    /**
     * The main exam interface.
     */
    public function show($p1, $p2 = null)
    {
        $id = $p2 ?? $p1;
        $session = $this->getValidSession($id, ['subject', 'examPackage']);
        $studentId = Auth::guard('student')->id();

        $attempt = ExamAttempt::where('exam_session_id', $id)
            ->where('student_id', $studentId)
            ->firstOrFail();

        if ($attempt->status === 'completed') {
            return redirect($this->dashboardRoute())
                ->with('error', 'Ujian sudah selesai.');
        }

        /**
         * Penting untuk hasil import Word:
         * - readingText harus ikut agar stimulus teks/gambar tampil.
         * - options harus diurutkan ID ASC agar A, B, C, D tidak acak.
         * - questions harus diurutkan ID ASC jika tidak ada kolom urutan paket.
         */
        if ($session->exam_package_id) {
            $questions = $session->examPackage->questions()
                ->with([
                    'options' => function ($query) {
                        $query->orderBy('id', 'asc');
                    },
                    'readingText',
                    'questionGroup',
                ])
                ->orderBy('questions.id', 'asc')
                ->get();
        } else {
            $questions = Question::where('subject_id', $session->subject_id)
                ->with([
                    'options' => function ($query) {
                        $query->orderBy('id', 'asc');
                    },
                    'readingText',
                    'questionGroup',
                ])
                ->orderBy('id', 'asc')
                ->get();
        }

        $sessionEndTime = $session->end_time;
        $attemptEndTime = $attempt->start_time->copy()->addMinutes($session->duration);
        $finalEndTime = $sessionEndTime < $attemptEndTime ? $sessionEndTime : $attemptEndTime;
        $remainingSeconds = now()->diffInSeconds($finalEndTime, false);

        if ($remainingSeconds <= 0) {
            $this->autoFinish($attempt, $session);

            return redirect($this->dashboardRoute())
                ->with('success', 'Waktu pengerjaan telah habis. Ujian otomatis disimpan.');
        }

        $savedAnswers = ExamAnswer::where('exam_attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        return view('student.exam.show', compact(
            'session',
            'questions',
            'attempt',
            'remainingSeconds',
            'savedAnswers'
        ));
    }

    /**
     * Store answer via AJAX.
     */
    public function storeAnswer(Request $request)
    {
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|exists:question_options,id',
            'essay_answer' => 'nullable|string',
            'answer_text' => 'nullable|string',
            'type' => 'nullable|string',
            'complex_answer' => 'nullable',
            'is_doubtful' => 'nullable',
        ]);

        $studentId = Auth::guard('student')->id();

        $attempt = ExamAttempt::where('exam_session_id', $request->exam_session_id)
            ->where('student_id', $studentId)
            ->firstOrFail();

        if ($attempt->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ujian sudah selesai.',
            ], 403);
        }

        $answer = ExamAnswer::where('exam_attempt_id', $attempt->id)
            ->where('question_id', $request->question_id)
            ->first();

        $data = [];

        // Multiple choice tunggal.
        if ($request->filled('option_id') && $request->type === 'multiple_choice') {
            $option = QuestionOption::find($request->option_id);

            $data['question_option_id'] = $request->option_id;
            $data['is_correct'] = $option ? (bool) $option->is_correct : false;
        }

        // Multiple choice complex dan boolean grid.
        if ($request->has('complex_answer')) {
            $complexAnswer = $request->complex_answer;

            $data['answer_text'] = is_array($complexAnswer) || is_object($complexAnswer)
                ? json_encode($complexAnswer)
                : $complexAnswer;

            $data['question_option_id'] = null;
        }

        // Ragu-ragu.
        if ($request->has('is_doubtful')) {
            $data['is_doubtful'] = filter_var($request->is_doubtful, FILTER_VALIDATE_BOOLEAN);
        }

        // Essay / fallback answer text.
        if ($request->has('essay_answer') || $request->has('answer_text')) {
            if (!isset($data['answer_text'])) {
                $data['answer_text'] = $request->input('essay_answer') ?? $request->input('answer_text');
            }
        }

        if ($answer) {
            $answer->update($data);
        } else {
            $data['exam_attempt_id'] = $attempt->id;
            $data['question_id'] = $request->question_id;

            ExamAnswer::create($data);
        }

        return response()->json(['status' => 'success']);
    }

    public function finish(Request $request, $p1, $p2 = null)
    {
        $id = $p2 ?? $p1;
        $session = $this->getValidSession($id);
        $studentId = Auth::guard('student')->id();

        $attempt = ExamAttempt::where('exam_session_id', $id)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $attempt->status = 'completed';
        $attempt->end_time = now();
        $attempt->save();

        $examService = new \App\Services\ExamService();
        $finalScore = $examService->gradeAttempt($attempt);

        $message = $session->show_score
            ? 'Ujian telah selesai. Nilai Anda: ' . number_format($finalScore, 2)
            : 'Ujian telah selesai. Jawaban Anda telah disimpan.';

        return redirect($this->dashboardRoute())->with('success', $message);
    }

    public function reportCheat(Request $request)
    {
        $studentId = Auth::guard('student')->id();

        if (!$request->exam_session_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session ID required',
            ], 400);
        }

        $attempt = ExamAttempt::where('exam_session_id', $request->exam_session_id)
            ->where('student_id', $studentId)
            ->firstOrFail();

        if ($attempt->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Exam already finished',
            ], 403);
        }

        // Use session to track warning count (not persisted to DB for tier 1)
        $sessionKey = 'warn_count_attempt_' . $attempt->id;
        $warningCount = session($sessionKey, 0) + 1;
        session([$sessionKey => $warningCount]);

        // Tier 1: First 3 violations → reduce time by 2 minutes each (120 seconds)
        // Tier 2: 4th violation onwards → increment cheat_count (existing penalty system)
        $timePenalty = 0;
        $isCheating = false;

        if ($warningCount <= 3) {
            // Only time penalty, do NOT increment cheat_count
            $timePenalty = 120; // 2 minutes per violation
        } else {
            // Real cheat: increment cheat_count
            $attempt->increment('cheat_count');
            $isCheating = true;
        }

        return response()->json([
            'status' => 'success',
            'tier' => $isCheating ? 'cheat' : 'warning',
            'warning_count' => $warningCount,
            'time_penalty' => $timePenalty,
            'current_cheat_count' => $attempt->cheat_count,
        ]);
    }

    /**
     * Internal logic for auto finishing expired attempts.
     */
    private function autoFinish(ExamAttempt $attempt, ExamSession $session): void
    {
        if ($attempt->status !== 'in_progress') {
            return;
        }

        $attempt->status = 'completed';

        $attemptEndTime = $attempt->start_time->copy()->addMinutes($session->duration);
        $finalEndTime = $session->end_time < $attemptEndTime ? $session->end_time : $attemptEndTime;

        $attempt->end_time = $finalEndTime;
        $attempt->save();

        try {
            $examService = new \App\Services\ExamService();
            $examService->gradeAttempt($attempt);
        } catch (\Throwable $e) {
            Log::error('Auto Finish Grading Error: ' . $e->getMessage());
        }
    }

    private function dashboardRoute(): string
    {
        return request()->route('subdomain')
            ? route('institution.student.dashboard', request()->route('subdomain'))
            : route('student.dashboard');
    }

    private function confirmationRoute(int $sessionId): string
    {
        return request()->route('subdomain')
            ? route('institution.student.exam.confirmation', [
                'subdomain' => request()->route('subdomain'),
                'id' => $sessionId,
            ])
            : route('student.exam.confirmation', $sessionId);
    }

    /**
     * Get a valid Exam Session strictly scoped to the student's institution.
     * Prevents data leakage between different schools.
     */
    private function getValidSession($id, array $withRelations = [])
    {
        $student = Auth::guard('student')->user();
        $adminId = $student->created_by ?? ($student->user ? $student->user->created_by : null);

        if (!$adminId) {
            $validCreatorIds = [];
        } else {
            $validCreatorIds = \App\Models\User::where('id', $adminId)
                                    ->orWhere('created_by', $adminId)
                                    ->pluck('id');
        }

        $query = ExamSession::whereHas('subject', function ($query) use ($validCreatorIds) {
            $query->whereIn('created_by', $validCreatorIds);
        });

        if (!empty($withRelations)) {
            $query->with($withRelations);
        }

        return $query->findOrFail($id);
    }
}
