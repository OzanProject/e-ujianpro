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
        
        $attempt = ExamAttempt::with(['examSession.subject', 'examSession.examPackage', 'answers.question.options', 'answers.option'])
                    ->where('id', $id)
                    ->where('student_id', $studentId)
                    ->firstOrFail();

        return view('student.history.show', compact('attempt'));
    }
}
