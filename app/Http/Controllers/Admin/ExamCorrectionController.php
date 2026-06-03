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

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $search = $request->input('search');
        $subjectId = $request->input('subject_id');
        $sort = $request->input('sort', 'az');
        $limit = $request->input('limit', 10);

        // Filter sessions by teacher subjects or admin creator
        $query = ExamSession::with(['subject'])->withCount(['attempts']);

        if ($user->role === 'pengajar') {
            $allowedSubjectIds = $user->subjects->pluck('id');
            $query->whereIn('subject_id', $allowedSubjectIds);
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $allowedSubjectIds = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
            $query->whereIn('subject_id', $allowedSubjectIds);
        }

        if ($search) {
            $query->whereHas('subject', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        if ($sort === 'az') {
            $query->orderBy(\App\Models\Subject::select('name')->whereColumn('subjects.id', 'exam_sessions.subject_id'), 'asc');
        } elseif ($sort === 'za') {
            $query->orderBy(\App\Models\Subject::select('name')->whereColumn('subjects.id', 'exam_sessions.subject_id'), 'desc');
        } elseif ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy(\App\Models\Subject::select('name')->whereColumn('subjects.id', 'exam_sessions.subject_id'), 'asc');
        }

        $sessions = $query->paginate($limit)->withQueryString();
        $baseRoute = $this->getBaseRoute();
        
        // Pass subjects for filter dropdown
        $subjects = \App\Models\Subject::whereIn('id', $allowedSubjectIds)->orderBy('name', 'asc')->get();

        return view('admin.correction.index', compact('sessions', 'baseRoute', 'subjects', 'search', 'subjectId', 'sort', 'limit'));
    }

    public function show(Request $request, $sessionId)
    {
        $user = auth()->user();
        $session = ExamSession::findOrFail($sessionId);

        // Security Check
        if ($user->role === 'pengajar' && !$user->subjects->contains($session->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $limit = $request->input('limit', 20);
        $sort = $request->input('sort', 'latest');

        $query = ExamAttempt::where('exam_session_id', $sessionId)
                            ->with('student');

        if ($sort === 'highest') {
            $query->orderBy('score', 'desc');
        } elseif ($sort === 'lowest') {
            $query->orderBy('score', 'asc');
        } else {
            $query->latest();
        }

        $attempts = $query->paginate($limit)->withQueryString();

        $baseRoute = $this->getBaseRoute();
        return view('admin.correction.show', compact('session', 'attempts', 'baseRoute', 'limit', 'sort'));
    }

    public function edit($attemptId)
    {
        $user = auth()->user();
        $attempt = ExamAttempt::with([
            'student', 
            'examSession.subject', 
            'answers.question.options', 
            'answers.option'
        ])->findOrFail($attemptId);
        
        // Security Check
        if ($user->role === 'pengajar' && !$user->subjects->contains($attempt->examSession->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.correction.edit', compact('attempt', 'baseRoute'));
    }

    public function update(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::with(['examSession'])->findOrFail($attemptId);
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
