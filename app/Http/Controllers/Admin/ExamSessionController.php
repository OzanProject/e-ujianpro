<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\ExamType; // Added Import
use App\Models\Subject;
use App\Models\ExamPackage;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.exam_session' : 'admin.exam_session';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Scope by Subjects owned by this user (Admin Lembaga) or Assigned (Teacher)
        if ($user->role === 'pengajar') {
            $subjectIds = $user->subjects->pluck('id');
        } else {
            // Admin/Operator: Filter by their subjects
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjectIds = Subject::where('created_by', $creatorId)->pluck('id');
        }

        $perPage = in_array((int) request('per_page', 10), [10, 20, 50, 100])
            ? (int) request('per_page', 10)
            : 10;

        $examSessions = ExamSession::whereIn('subject_id', $subjectIds)
            ->with(['subject', 'examPackage'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        // Lazy Backfill: Generate token if missing (Only for items in current page to be efficient)
        foreach ($examSessions as $session) {
            if (empty($session->token)) {
                $session->token = strtoupper(\Illuminate\Support\Str::random(5));
                $session->save();
            }
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.exam_session.index', compact('examSessions', 'baseRoute'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
            // For teachers, we look for exam types of the person who created this teacher
            $examTypes = ExamType::where('created_by', $user->created_by)->where('is_active', true)->get();
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = Subject::where('created_by', $creatorId)->get();
            $examTypes = ExamType::where('created_by', $creatorId)->where('is_active', true)->get();
        }

        // Get Packages suitable for these subjects
        $packages = ExamPackage::whereIn('subject_id', $subjects->pluck('id'))->get();

        $baseRoute = $this->getBaseRoute();
        return view('admin.exam_session.create', compact('subjects', 'packages', 'examTypes', 'baseRoute'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_package_id' => 'nullable|exists:exam_packages,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'target_kelas' => 'nullable|string',
        ], [
            'end_time.after' => 'Waktu Selesai harus setelah Waktu Mulai.',
            'start_time.required' => 'Waktu Mulai wajib diisi.',
            'end_time.required' => 'Waktu Selesai wajib diisi.',
        ]);

        // Security Check
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            return back()->withInput()->with('error', 'Akses Ditolak. Anda tidak mengampu mata pelajaran ini.');
        }

        try {
            $data = $request->all();

            // Get Title from Exam Type
            $examType = ExamType::find($request->exam_type_id);
            $data['title'] = $examType->name; 

            // Generate a 5-character uppercase random token
            $data['token'] = strtoupper(\Illuminate\Support\Str::random(5));
            $data['created_by'] = $user->id;

            ExamSession::create($data);

            return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        $user = auth()->user();

        // Security Check
        $isOwner = false;
        if ($user->role === 'pengajar') {
            $isOwner = $user->subjects->contains($examSession->subject_id);
            $examTypes = ExamType::where('created_by', $user->created_by)->where('is_active', true)->get();
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subject = Subject::find($examSession->subject_id);
            $isOwner = $subject && $subject->created_by == $creatorId;
            $examTypes = ExamType::where('created_by', $creatorId)->where('is_active', true)->get();
        }

        if (!$isOwner) {
            abort(403, 'Akses Ditolak.');
        }

        // Fetch subjects for dropdown
        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = Subject::where('created_by', $creatorId)->get();
        }

        $packages = ExamPackage::where('subject_id', $examSession->subject_id)->get();

        $baseRoute = $this->getBaseRoute();
        return view('admin.exam_session.edit', compact('examSession', 'subjects', 'packages', 'examTypes', 'baseRoute'));
    }

    public function update(Request $request, string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        $user = auth()->user();

        // Security Check
        $isOwner = false;
        if ($user->role === 'pengajar') {
            $isOwner = $user->subjects->contains($examSession->subject_id);
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subject = Subject::find($examSession->subject_id);
            $isOwner = $subject && $subject->created_by == $creatorId;
        }

        if (!$isOwner) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_package_id' => 'nullable|exists:exam_packages,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'target_kelas' => 'nullable|string',
        ], [
            'end_time.after' => 'Waktu Selesai harus setelah Waktu Mulai.',
            'start_time.required' => 'Waktu Mulai wajib diisi.',
            'end_time.required' => 'Waktu Selesai wajib diisi.',
        ]);

        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            return back()->withInput()->with('error', 'Akses Ditolak. Anda tidak mengampu mata pelajaran ini.');
        }

        try {
            $data = $request->except(['_token', '_method', 'is_active', 'show_score']);
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            $data['show_score'] = $request->has('show_score') ? 1 : 0; 

            $examType = ExamType::find($request->exam_type_id);
            $data['title'] = $examType->name;

            $examSession->update($data);

            return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Jadwal ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        
        // Security Check for delete
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($examSession->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $examSession->delete();
        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }

    public function regenerateToken(string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        
        // Security Check
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($examSession->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $examSession->token = strtoupper(\Illuminate\Support\Str::random(5));
        $examSession->save();

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Token ujian berhasil diperbarui.');
    }
    public function bulkRegenerateToken(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:exam_sessions,id'
        ]);

        $user = auth()->user();
        $sessions = ExamSession::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($sessions as $session) {
            // Security Check
            $canEdit = false;
            if ($user->role === 'pengajar') {
                $canEdit = $user->subjects->contains($session->subject_id);
            } else {
                $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
                $subject = Subject::find($session->subject_id);
                $canEdit = $subject && $subject->created_by == $creatorId;
            }

            if ($canEdit) {
                $session->token = strtoupper(\Illuminate\Support\Str::random(5));
                $session->save();
                $count++;
            }
        }

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', "$count token ujian berhasil diperbarui masal.");
    }
    public function assignProctors(ExamSession $examSession)
    {
        $baseRoute = $this->getBaseRoute();
        $institutionId = auth()->user()->getInstitutionId();
        $teachers = \App\Models\User::where('role', 'pengajar')->where('created_by', $institutionId)->get();
        $rooms = \App\Models\ExamRoom::where('created_by', $institutionId)->get();
        $assignments = $examSession->proctors()->get();

        return view('admin.exam_session.assign_proctors', compact('examSession', 'baseRoute', 'teachers', 'rooms', 'assignments'));
    }

    public function storeProctors(Request $request, ExamSession $examSession)
    {
        $request->validate([
            'assignments' => 'array',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.exam_room_id' => 'required|exists:exam_rooms,id',
        ]);

        $examSession->proctors()->detach();

        if ($request->has('assignments') && is_array($request->assignments)) {
            foreach ($request->assignments as $assignment) {
                $examSession->proctors()->attach($assignment['user_id'], ['exam_room_id' => $assignment['exam_room_id']]);
            }
        }

        $baseRoute = $this->getBaseRoute();
        return redirect()->route($baseRoute . '.index')->with('success', 'Pengawas berhasil ditugaskan.');
    }

    public function duplicate(string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        $user = auth()->user();

        // Security Check
        $canEdit = false;
        if ($user->role === 'pengajar') {
            $canEdit = $user->subjects->contains($examSession->subject_id);
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subject = Subject::find($examSession->subject_id);
            $canEdit = $subject && $subject->created_by == $creatorId;
        }

        if (!$canEdit) {
            abort(403, 'Akses Ditolak.');
        }

        $newSession = $examSession->replicate();
        $newSession->title = $newSession->title . ' (Copy)';
        $newSession->token = strtoupper(\Illuminate\Support\Str::random(5));
        $newSession->save();

        // Copy proctors
        if ($examSession->proctors()->exists()) {
            foreach ($examSession->proctors as $proctor) {
                $newSession->proctors()->attach($proctor->id, ['exam_room_id' => $proctor->pivot->exam_room_id]);
            }
        }

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Jadwal ujian berhasil diduplikasi. Silakan edit target kelas atau informasi lainnya pada jadwal baru ini.');
    }
}

