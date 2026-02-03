<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class ExamCorrectionController extends Controller
{
    public function index()
    {
        // Show List of Exam Sessions that have attempts
        $sessions = ExamSession::withCount(['attempts' => function($query) {
            $query->where('status', 'completed');
        }])->latest()->paginate(10);
        
        return view('admin.correction.index', compact('sessions'));
    }

    public function show($sessionId)
    {
        // Show List of Attempts for a Session
        $session = ExamSession::findOrFail($sessionId);
        $attempts = ExamAttempt::where('exam_session_id', $sessionId)
                                ->where('status', 'completed')
                                ->with('student')
                                ->latest()
                                ->paginate(20);

        return view('admin.correction.show', compact('session', 'attempts'));
    }

    public function edit($attemptId)
    {
        // Show Grading Interface
        $attempt = ExamAttempt::with(['student', 'examSession.subject', 'answers.question'])->findOrFail($attemptId);
        
        return view('admin.correction.edit', compact('attempt'));
    }

    public function update(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);
        
        $request->validate([
            'scores' => 'array',
            'scores.*' => 'numeric|min:0',
        ]);

        $totalScore = 0;
        
        // Loop through submitted scores
        if($request->scores) {
            foreach ($request->scores as $answerId => $score) {
                $answer = $attempt->answers()->find($answerId);
                if ($answer) {
                    $answer->score = $score;
                    $answer->save();
                }
            }
        }

        // Recalculate Total Score from DB to be safe
        $totalScore = $attempt->answers()->sum('score');
        $attempt->score = $totalScore;
        $attempt->save();

        return redirect()->route('admin.correction.show', $attempt->exam_session_id)
                         ->with('success', 'Nilai berhasil disimpan. Total Skor: ' . $totalScore);
    }
}
