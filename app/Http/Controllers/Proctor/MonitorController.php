<?php

namespace App\Http\Controllers\Proctor;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function show($subdomain, $id)
    {
        $session = ExamSession::findOrFail($id);
        $proctor = auth()->user();
        
        $query = ExamAttempt::where('exam_session_id', $id)
                    ->with('student');

        if ($proctor->exam_room_id) {
            $query->whereHas('student', function($q) use ($proctor) {
                $q->where('exam_room_id', $proctor->exam_room_id);
            });
        }

        $attempts = $query->orderBy('updated_at', 'desc')->get();

        return view('proctor.monitor', compact('session', 'attempts'));
    }

    public function getData($subdomain, $id)
    {
        $session = ExamSession::findOrFail($id);
        $proctor = auth()->user();
        
        $query = ExamAttempt::where('exam_session_id', $id)
                    ->with('student');

        // Filter by Room if Proctor is assigned to one
        if ($proctor->exam_room_id) {
            $query->whereHas('student', function($q) use ($proctor) {
                $q->where('exam_room_id', $proctor->exam_room_id);
            });
        }

        $attempts = $query->orderBy('updated_at', 'desc')->get();
        
        // Transform for JSON
        $data = $attempts->map(function($attempt) {
            return [
                'id' => $attempt->id,
                'student_name' => $attempt->student->name ?? 'Unknown',
                'student_number' => $attempt->student->nisn ?? '-', // Assuming NISN or similar
                'start_time' => $attempt->start_time ? $attempt->start_time->format('H:i:s') : '-',
                'status' => $attempt->status,
                'score' => $attempt->score ?? '-',
                'last_activity' => $attempt->updated_at->diffForHumans(),
                'is_online' => $attempt->updated_at->diffInMinutes(now()) < 5, // Simple online check
            ];
        });

        return response()->json($data);
    }

    public function reset($subdomain, $id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        // Reset status to in_progress if needed
        $attempt->status = 'in_progress';
        $attempt->end_time = null; // Clear end time if set
        $attempt->save();

        return response()->json(['success' => true, 'message' => 'Login siswa berhasil di-reset.']);
    }

    public function stop($subdomain, $id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        $attempt->status = 'completed';
        $attempt->end_time = now();
        $attempt->save();

        // Ideally triggers auto-grading here (call ExamController::finish logic?)
        // For MVP, we just mark as complete. Grading usually happens on finish() call.
        // To be safe, we might need to duplicate grading logic or redirect.
        // But "Stop Paksa" implies just stopping them. Grading can be done later or manually correction.

        return response()->json(['success' => true, 'message' => 'Ujian siswa berhasil dihentikan.']);
    }
}
