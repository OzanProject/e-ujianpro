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

        $assignedRooms = \DB::table('exam_session_proctors')
            ->where('exam_session_id', $id)
            ->where('user_id', $proctor->id)
            ->pluck('exam_room_id')->toArray();

        if (count($assignedRooms) > 0) {
            $query->whereHas('student', function($q) use ($assignedRooms) {
                $q->whereIn('exam_room_id', $assignedRooms);
            });
        } elseif ($proctor->exam_room_id) {
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
        
        $adminId = $proctor->getInstitutionId();

        $studentsQuery = \App\Models\Student::where(function($q) use ($adminId) {
            $q->where('created_by', $adminId)
              ->orWhereHas('user', function($uq) use ($adminId) {
                  $uq->where('created_by', $adminId);
              });
        });

        // Filter by Room if Proctor is assigned to one
        $assignedRooms = \DB::table('exam_session_proctors')
            ->where('exam_session_id', $id)
            ->where('user_id', $proctor->id)
            ->pluck('exam_room_id')->toArray();

        if (count($assignedRooms) > 0) {
            $studentsQuery->whereIn('exam_room_id', $assignedRooms);
        } elseif ($proctor->exam_room_id) {
            $studentsQuery->where('exam_room_id', $proctor->exam_room_id);
        }

        $allStudents = $studentsQuery->get();

        // Filter valid students based on target_kelas (matching Dashboard logic)
        $validStudents = $allStudents->filter(function($student) use ($session) {
            if (!$session->target_kelas) return true;

            $parts = explode(' ', str_replace('-', ' ', $student->kelas));
            $first = strtoupper($parts[0] ?? '');
            $romans = [
                'TK' => 0, 'PAUD' => 0,
                'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6,
                'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10, 'XI' => 11, 'XII' => 12, 'XIII' => 13
            ];
            $studentLevel = null;
            if (isset($romans[$first])) {
                $studentLevel = (string)$romans[$first];
            } elseif (is_numeric($first)) {
                $studentLevel = (string)intval($first);
            }

            return (string)$studentLevel === (string)$session->target_kelas;
        });

        // Get attempts only for these valid students
        $attempts = ExamAttempt::where('exam_session_id', $id)
                    ->whereIn('student_id', $validStudents->pluck('id'))
                    ->get();
        
        // Transform for JSON
        $data = $validStudents->map(function($student) use ($attempts) {
            $attempt = $attempts->firstWhere('student_id', $student->id);
            
            if ($attempt) {
                return [
                    'id' => $attempt->id,
                    'student_name' => $student->name ?? 'Unknown',
                    'student_number' => $student->nisn ?? '-',
                    'start_time' => $attempt->start_time ? $attempt->start_time->format('H:i:s') : '-',
                    'status' => $attempt->status,
                    'score' => is_numeric($attempt->score) ? number_format($attempt->score, 2) : ($attempt->score ?? '-'),
                    'last_activity' => $attempt->updated_at->diffForHumans(),
                    'is_online' => $attempt->updated_at->diffInMinutes(now()) < 5,
                    'cheat_count' => $attempt->cheat_count,
                    'updated_at_ts' => $attempt->updated_at->timestamp,
                ];
            } else {
                return [
                    'id' => 'student_' . $student->id, // Fake ID so it doesn't break JS
                    'student_name' => $student->name ?? 'Unknown',
                    'student_number' => $student->nisn ?? '-',
                    'start_time' => '-',
                    'status' => 'Belum Mulai',
                    'score' => '-',
                    'last_activity' => '-',
                    'is_online' => false,
                    'cheat_count' => 0,
                    'updated_at_ts' => 0,
                ];
            }
        })->values();

        return response()->json($data);
    }

    public function reset($subdomain, $id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        // Reset status to in_progress and update start_time to now() so their duration timer resets
        $attempt->status = 'in_progress';
        $attempt->start_time = now();
        $attempt->end_time = null; // Clear end time if set
        $attempt->cheat_count = 0; // Reset cheating violations
        $attempt->save();

        // Also check if the global session has expired or is close to expiring. If so, extend it!
        $session = $attempt->examSession;
        if ($session) {
            $requiredEndTime = now()->addMinutes($session->duration + 15);
            if ($session->end_time < $requiredEndTime || !$session->is_active) {
                $session->end_time = $requiredEndTime;
                $session->is_active = true;
                $session->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Login & waktu siswa berhasil di-reset. Siswa dapat melanjutkan ujian.']);
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
