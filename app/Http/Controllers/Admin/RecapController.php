<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecapController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.recap' : 'admin.recap';
    }

    public function examResult(Request $request)
    {
        $user = Auth::user();
        
        // Scope Exam Sessions
        if ($user->role === 'pengajar') {
            $examSessions = ExamSession::whereIn('subject_id', $user->subjects->pluck('id'))
                                ->with(['subject', 'examPackage'])
                                ->orderByDesc('start_time')
                                ->get();
        } else {
            $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
            $subjects = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
            $examSessions = ExamSession::whereIn('subject_id', $subjects)
                                ->with(['subject', 'examPackage'])
                                ->orderByDesc('start_time')
                                ->get();
        }

        $selectedSession = null;
        $attempts = collect([]);
        $summary = [
            'total_students' => 0,
            'avg_score' => 0,
            'max_score' => 0,
            'min_score' => 0,
            'passed' => 0,
            'failed' => 0
        ];

        if ($request->has('exam_session_id') && $request->exam_session_id) {
            // Verify ownership
            $sessionCheck = $examSessions->where('id', $request->exam_session_id)->first();
            
            if ($sessionCheck) {
                $selectedSession = ExamSession::with('examPackage')->find($request->exam_session_id);
                
                $attempts = $selectedSession->attempts()
                    ->with(['student.group', 'student', 'answers.option', 'answers.question.options']) 
                    ->orderByDesc('score')
                    ->get();

                if ($attempts->count() > 0) {
                    $summary['total_students'] = $attempts->count();
                    $summary['avg_score'] = $attempts->avg('score');
                    $summary['max_score'] = $attempts->max('score');
                    $summary['min_score'] = $attempts->min('score');
                    $kkm = $selectedSession->subject->kkm ?? 75;
                    $summary['passed'] = $attempts->where('score', '>=', $kkm)->count(); 
                    $summary['failed'] = $attempts->where('score', '<', $kkm)->count();
                }
            }
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.recap.exam_result', compact('examSessions', 'selectedSession', 'attempts', 'summary', 'baseRoute'));
    }

    public function printExamResult(Request $request)
    {
        $user = Auth::user();
        $selectedSession = ExamSession::with(['examPackage.subject', 'examPackage.subject.creator'])->find($request->exam_session_id);

        if (!$selectedSession) {
            abort(404);
        }

        $hasAccess = false;
        if ($user->role === 'pengajar') {
             $allowedSubjectIds = $user->subjects->pluck('id')->toArray();
             if (in_array($selectedSession->subject_id, $allowedSubjectIds)) {
                 $hasAccess = true;
             }
        } else {
             $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
             if ($selectedSession->subject->created_by == $creatorId) {
                 $hasAccess = true;
             }
        }

        if (!$hasAccess && $user->role !== 'super_admin') {
            abort(403, 'Unauthorized access.');
        }

        $attempts = $selectedSession->attempts()
            ->with('student.group', 'student', 'answers')
            ->orderByDesc('score')
            ->get();

        $subjectOwnerId = $selectedSession->subject->created_by;
        $institution = \App\Models\Institution::where('user_id', $subjectOwnerId)->first();

        if (!$institution) {
             $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
             $institution = \App\Models\Institution::where('user_id', $creatorId)->first();
        }

        return view('admin.recap.print_exam_result', compact('selectedSession', 'attempts', 'institution'));
    }

    public function resetExamResult(Request $request, $exam_session_id)
    {
        $user = Auth::user();
        $session = ExamSession::with('subject')->findOrFail($exam_session_id);

        $hasAccess = false;
        if ($user->role === 'pengajar') {
            $allowedSubjectIds = $user->subjects->pluck('id')->toArray();
            $hasAccess = in_array($session->subject_id, $allowedSubjectIds);
        } else {
            $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
            $hasAccess = ($session->subject->created_by == $creatorId);
        }

        if (!$hasAccess && $user->role !== 'super_admin') {
            abort(403, 'Unauthorized access.');
        }

        // 1. Hapus semua jawaban dan attempt pada sesi ini
        $attemptIds = $session->attempts()->pluck('id');
        \App\Models\ExamAnswer::whereIn('exam_attempt_id', $attemptIds)->delete();
        $session->attempts()->delete();

        // 2. Pastikan sesi AKTIF agar siswa bisa mengikuti ujian lagi
        $sessionUpdates = [];
        $notes = [];

        // Aktifkan jika nonaktif
        if (!$session->is_active) {
            $sessionUpdates['is_active'] = true;
            $notes[] = 'Sesi diaktifkan kembali';
        }

        // Jika end_time sudah lewat, perpanjang ke 2 jam dari sekarang
        if ($session->end_time < now()) {
            $sessionUpdates['end_time'] = now()->addHours(2);
            $notes[] = 'Waktu ujian diperpanjang hingga 2 jam ke depan (' . now()->addHours(2)->format('H:i, d M Y') . ')';
        }

        if (!empty($sessionUpdates)) {
            $session->update($sessionUpdates);
        }

        $message = 'Semua hasil ujian pada sesi ini berhasil direset. Siswa kini dapat mengikuti ujian kembali.';
        if (!empty($notes)) {
            $message .= ' Catatan: ' . implode('; ', $notes) . '.';
        }

        return redirect()
            ->route($this->getBaseRoute() . '.exam_result', ['exam_session_id' => $exam_session_id])
            ->with('success', $message);
    }

    public function deleteExamAttempt(Request $request, $id)
    {
        $attempt = \App\Models\ExamAttempt::with('examSession.subject')->findOrFail($id);
        $session = $attempt->examSession;
        $user = Auth::user();

        $hasAccess = false;
        if ($user->role === 'pengajar') {
            $allowedSubjectIds = $user->subjects->pluck('id')->toArray();
            $hasAccess = in_array($session->subject_id, $allowedSubjectIds);
        } else {
            $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
            $hasAccess = ($session->subject->created_by == $creatorId);
        }

        if (!$hasAccess && $user->role !== 'super_admin') {
            abort(403, 'Unauthorized access.');
        }

        $attempt->answers()->delete();
        $attempt->delete();

        // Pastikan sesi aktif agar siswa bisa mengikuti ujian lagi
        $sessionUpdates = [];
        $notes = [];

        if (!$session->is_active) {
            $sessionUpdates['is_active'] = true;
            $notes[] = 'sesi diaktifkan kembali';
        }

        if ($session->end_time < now()) {
            $sessionUpdates['end_time'] = now()->addHours(2);
            $notes[] = 'waktu diperpanjang 2 jam ke depan';
        }

        if (!empty($sessionUpdates)) {
            $session->update($sessionUpdates);
        }

        $message = 'Hasil ujian siswa berhasil dihapus. Siswa dapat mengikuti ujian kembali.';
        if (!empty($notes)) {
            $message .= ' Catatan: ' . implode(', ', $notes) . '.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function recalculateSessionScores(Request $request, $exam_session_id)
    {
        $user = Auth::user();
        $session = ExamSession::with('subject')->findOrFail($exam_session_id);

        $hasAccess = false;
        if ($user->role === 'pengajar') {
            $allowedSubjectIds = $user->subjects->pluck('id')->toArray();
            $hasAccess = in_array($session->subject_id, $allowedSubjectIds);
        } else {
            $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
            $hasAccess = ($session->subject->created_by == $creatorId);
        }

        if (!$hasAccess && $user->role !== 'super_admin') {
            abort(403, 'Unauthorized access.');
        }

        $attempts = $session->attempts()->get();
        $examService = new \App\Services\ExamService();

        $count = 0;
        foreach ($attempts as $attempt) {
            $examService->gradeAttempt($attempt);
            $count++;
        }

        return redirect()
            ->route($this->getBaseRoute() . '.exam_result', ['exam_session_id' => $exam_session_id])
            ->with('success', "Berhasil menghitung ulang nilai untuk $count peserta ujian. Semua nilai kompleks dan grid kini sudah terkoreksi dengan benar.");
    }

    public function itemAnalysis(Request $request)
    {
        $user = Auth::user();
        
        // Scope Exam Sessions
        if ($user->role === 'pengajar') {
            $examSessions = ExamSession::whereIn('subject_id', $user->subjects->pluck('id'))
                                ->with(['subject', 'examPackage'])
                                ->orderByDesc('start_time')
                                ->get();
        } else {
            $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
            $subjects = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
            $examSessions = ExamSession::whereIn('subject_id', $subjects)
                                ->with(['subject', 'examPackage'])
                                ->orderByDesc('start_time')
                                ->get();
        }

        $selectedSession = null;
        $attempts = collect([]);
        $questions = collect([]);
        $kkm = $request->input('kkm', null);

        if ($request->has('exam_session_id') && $request->exam_session_id) {
            $sessionCheck = $examSessions->where('id', $request->exam_session_id)->first();
            
            if ($sessionCheck) {
                $selectedSession = ExamSession::with(['examPackage.questions.options'])->find($request->exam_session_id);
                $questions = $selectedSession->examPackage->questions;
                
                $attempts = $selectedSession->attempts()
                    ->with(['student.group', 'student', 'answers.option', 'answers.question']) 
                    ->orderByDesc('score')
                    ->get();
                
                if (is_null($kkm)) {
                    $kkm = $selectedSession->subject->kkm ?? 75;
                }
            }
        }
        
        if (is_null($kkm)) {
            $kkm = 75;
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.recap.item_analysis', compact('examSessions', 'selectedSession', 'attempts', 'questions', 'kkm', 'baseRoute'));
    }

    public function printItemAnalysis(Request $request)
    {
        $user = Auth::user();
        $selectedSession = ExamSession::with(['examPackage.questions.options', 'examPackage.subject.creator'])->find($request->exam_session_id);

        if (!$selectedSession) {
            abort(404);
        }

        $hasAccess = false;
        if ($user->role === 'pengajar') {
             $allowedSubjectIds = $user->subjects->pluck('id')->toArray();
             if (in_array($selectedSession->subject_id, $allowedSubjectIds)) {
                 $hasAccess = true;
             }
        } else {
             $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
             if ($selectedSession->subject->created_by == $creatorId) {
                 $hasAccess = true;
             }
        }

        if (!$hasAccess && $user->role !== 'super_admin') {
            abort(403, 'Unauthorized access.');
        }

        $kkm = $request->input('kkm', $selectedSession->subject->kkm ?? 75);
        $questions = $selectedSession->examPackage->questions;

        $attempts = $selectedSession->attempts()
            ->with(['student.group', 'student', 'answers.option', 'answers.question'])
            ->orderByDesc('score')
            ->get();

        $subjectOwnerId = $selectedSession->subject->created_by;
        $institution = \App\Models\Institution::where('user_id', $subjectOwnerId)->first();

        if (!$institution) {
             $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
             $institution = \App\Models\Institution::where('user_id', $creatorId)->first();
        }

        return view('admin.recap.print_item_analysis', compact('selectedSession', 'attempts', 'questions', 'kkm', 'institution'));
    }
}
