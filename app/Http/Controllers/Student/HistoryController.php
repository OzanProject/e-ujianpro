<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $studentId = Auth::guard('student')->id();
        
        $attempts = ExamAttempt::with(['examSession.subject', 'examSession.examPackage'])
                        ->where('student_id', $studentId)
                        ->where('status', 'completed')
                        ->orderBy('end_time', 'desc')
                        ->paginate(10);

        return view('student.history.index', compact('attempts'));
    }

    public function show($p1, $p2 = null)
    {
        // Handle optional subdomain parameter
        $id = $p2 ?? $p1;
        $studentId = Auth::guard('student')->id();
        
        // 1. Get fundamental attempt data
        $attempt = ExamAttempt::with(['examSession.subject', 'examSession.examPackage'])
                    ->where('id', $id)
                    ->where('student_id', $studentId)
                    ->firstOrFail();

        // 2. Fetch all answers for statistics (before pagination)
        $allAnswers = $attempt->answers()->with(['question', 'option'])->get();
        
        $stats = [
            'total' => $allAnswers->count(),
            'correct' => 0,
            'wrong' => 0,
            'empty' => 0,
            'essay' => 0,
        ];

        foreach ($allAnswers as $ans) {
            $questionType = $ans->question->type ?? 'multiple_choice';

            if ($questionType === 'essay') {
                if ($ans->answer_text) {
                    $stats['essay']++;
                } else {
                    $stats['empty']++;
                }
            } else {
                // Objective types (PG Tunggal, PG Kompleks, Benar/Salah)
                if ($ans->question_option_id || $ans->answer_text) {
                    if ($ans->is_correct) {
                        $stats['correct']++;
                    } else {
                        $stats['wrong']++;
                    }
                } else {
                    $stats['empty']++;
                }
            }
        }

        // 3. Paginate answers for display
        $perPage = request()->get('per_page', 10);
        $answers = $attempt->answers()
                    ->with(['question.options', 'option'])
                    ->paginate($perPage)
                    ->appends(['per_page' => $perPage]); // Keep parameter in pagination links

        return view('student.history.show', compact('attempt', 'answers', 'stats', 'perPage'));
    }
}
