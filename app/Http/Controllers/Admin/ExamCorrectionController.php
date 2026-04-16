<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class ExamCorrectionController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.correction' : 'admin.correction';
    }

    public function index()
    {
        $user = auth()->user();
        
        // Filter sessions by teacher subjects or admin creator
        $query = ExamSession::withCount(['attempts' => function($query) {
            $query->where('status', 'completed');
        }]);

        if ($user->role === 'pengajar') {
            $query->whereIn('subject_id', $user->subjects->pluck('id'));
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjectIds = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
            $query->whereIn('subject_id', $subjectIds);
        }

        $sessions = $query->latest()->paginate(10);
        $baseRoute = $this->getBaseRoute();
        
        return view('admin.correction.index', compact('sessions', 'baseRoute'));
    }

    public function show($sessionId)
    {
        $user = auth()->user();
        $session = ExamSession::findOrFail($sessionId);

        // Security Check
        if ($user->role === 'pengajar' && !$user->subjects->contains($session->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $attempts = ExamAttempt::where('exam_session_id', $sessionId)
                                ->where('status', 'completed')
                                ->with('student')
                                ->latest()
                                ->paginate(20);

        $baseRoute = $this->getBaseRoute();
        return view('admin.correction.show', compact('session', 'attempts', 'baseRoute'));
    }

    public function edit($attemptId)
    {
        $user = auth()->user();
        $attempt = ExamAttempt::with(['student', 'examSession.subject', 'answers.question'])->findOrFail($attemptId);
        
        // Security Check
        if ($user->role === 'pengajar' && !$user->subjects->contains($attempt->examSession->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.correction.edit', compact('attempt', 'baseRoute'));
    }

    public function update(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::load(['examSession'])->findOrFail($attemptId);
        $user = auth()->user();

        // Security Check
        if ($user->role === 'pengajar' && !$user->subjects->contains($attempt->examSession->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }
        
        $request->validate([
            'scores' => 'array',
            'scores.*' => 'numeric|min:0',
        ]);

        if($request->scores) {
            foreach ($request->scores as $answerId => $score) {
                $answer = $attempt->answers()->find($answerId);
                if ($answer) {
                    $answer->score = $score;
                    $answer->save();
                }
            }
        }

        // Recalculate Total Score
        $totalScore = $attempt->answers()->sum('score');
        $attempt->score = $totalScore;
        $attempt->save();

        return redirect()->route($this->getBaseRoute() . '.show', $attempt->exam_session_id)
                         ->with('success', 'Nilai berhasil disimpan. Total Skor: ' . $totalScore);
    }
}
