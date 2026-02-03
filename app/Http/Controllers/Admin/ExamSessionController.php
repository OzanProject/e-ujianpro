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
            // Admin Lembaga: Get subjects created by them
            $subjectIds = Subject::where('created_by', $user->id)->pluck('id');
        }

        $examSessions = ExamSession::whereIn('subject_id', $subjectIds)
                            ->with('subject')
                            ->latest()
                            ->get();

        // Lazy Backfill: Generate token if missing
        foreach ($examSessions as $session) {
            if (empty($session->token)) {
                $session->token = strtoupper(\Illuminate\Support\Str::random(5));
                $session->save();
            }
        }

        $examSessions = ExamSession::whereIn('subject_id', $subjectIds)
                            ->with('subject')
                            ->latest()
                            ->paginate(10);
                            
        return view('admin.exam_session.index', compact('examSessions'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
        } else {
             // Admin Lembaga: Get subjects created by them
            $subjects = Subject::where('created_by', $user->id)->get();
        }

        // Get Packages suitable for these subjects
        $packages = ExamPackage::whereIn('subject_id', $subjects->pluck('id'))->get();
        
        // Get Exam Types
        $examTypes = ExamType::where('created_by', $user->id)->where('is_active', true)->get();

        return view('admin.exam_session.create', compact('subjects', 'packages', 'examTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id', // Changed from title required
            'subject_id' => 'required|exists:subjects,id',
            'exam_package_id' => 'nullable|exists:exam_packages,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->all();
            
            // Get Title from Exam Type
            $examType = ExamType::find($request->exam_type_id);
            $data['title'] = $examType->name; // Save title for backward compatibility
            
            // Generate a 5-character uppercase random token
            $data['token'] = strtoupper(\Illuminate\Support\Str::random(5));
            
            ExamSession::create($data);

            return redirect()->route('admin.exam_session.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage())->withInput();
        }
    }

    // ... show method unchanged ...

    public function edit(string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        $user = auth()->user();

        // Security Check: Ensure user owns the subject of this session
        $isOwner = false;
        if ($user->role === 'pengajar') {
            $isOwner = $user->subjects->contains($examSession->subject_id);
        } else {
            // Check if subject was created by this admin
            $subject = Subject::find($examSession->subject_id);
            $isOwner = $subject && $subject->created_by == $user->id;
        }

        if (!$isOwner) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mengedit sesi ujian ini.');
        }

        // Fetch subjects for dropdown
        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
        } else {
            $subjects = Subject::where('created_by', $user->id)->get();
        }
        
        $packages = ExamPackage::where('subject_id', $examSession->subject_id)->get();
        $examTypes = ExamType::where('created_by', $user->id)->where('is_active', true)->get();

        return view('admin.exam_session.edit', compact('examSession', 'subjects', 'packages', 'examTypes'));
    }

    public function update(Request $request, string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        $user = auth()->user();

        // ... Security Check omitted for brevity in prompt context ... 
        // Security Check: Ensure user owns the subject of this session
        $isOwner = false;
        if ($user->role === 'pengajar') {
            $isOwner = $user->subjects->contains($examSession->subject_id);
        } else {
            $subject = Subject::find($examSession->subject_id);
            $isOwner = $subject && $subject->created_by == $user->id;
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
        ]);

        try {
            $data = $request->except(['_token', '_method', 'is_active']);
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            
            // Update Title from Exam Type
            $examType = ExamType::find($request->exam_type_id);
            $data['title'] = $examType->name;

            $examSession->update($data);

            return redirect()->route('admin.exam_session.index')->with('success', 'Jadwal ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        $examSession->delete();
        return redirect()->back()->with('success', 'Jadwal ujian berhasil dihapus.');
    }

    /**
     * Regenerate Token for specific session
     */
    public function regenerateToken(string $id)
    {
        $examSession = ExamSession::findOrFail($id);
        $examSession->token = strtoupper(\Illuminate\Support\Str::random(5));
        $examSession->save();
        
        return redirect()->back()->with('success', 'Token ujian berhasil diperbarui.');
    }
}
