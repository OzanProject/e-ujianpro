<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeachersImport;
use App\Exports\TeacherTemplateExport;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil user dengan role 'pengajar' yang dibuat oleh user yang login
        $teachers = User::where('role', 'pengajar')
                        ->where('created_by', auth()->user()->getInstitutionId())
                        ->latest()
                        ->get();
        return view('admin.teacher.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = \App\Models\Subject::orderBy('name')->get();
        return view('admin.teacher.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'subjects' => ['required', 'array'], // Validasi subjects wajib dipilih
            'subjects.*' => ['exists:subjects,id'],
        ], [
            'subjects.required' => 'Pilih minimal satu mata pelajaran.',
        ]);

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pengajar',
            'created_by' => auth()->user()->getInstitutionId(), // Save Creator ID
        ]);

        // Simpan relasi mata pelajaran
        $teacher->subjects()->sync($request->subjects);

        return redirect()->route('admin.teacher.index')->with('success', 'Guru berhasil ditambahkan beserta mata pelajaran yang diampu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $teacher = User::where('role', 'pengajar')
                       ->where('created_by', auth()->user()->getInstitutionId())
                       ->findOrFail($id);
        
        // Pastikan yang diedit adalah pengajar
        if ($teacher->role !== 'pengajar') {
            abort(403);
        }

        $subjects = \App\Models\Subject::orderBy('name')->get();
        return view('admin.teacher.edit', compact('teacher', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = User::where('role', 'pengajar')
                       ->where('created_by', auth()->user()->getInstitutionId())
                       ->findOrFail($id);

        if ($teacher->role !== 'pengajar') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$teacher->id],
            'subjects' => ['required', 'array'],
            'subjects.*' => ['exists:subjects,id'],
        ], [
            'subjects.required' => 'Pilih minimal satu mata pelajaran.',
        ]);

        $teacher->name = $request->name;
        $teacher->email = $request->email;
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $teacher->password = Hash::make($request->password);
        }

        $teacher->save();

        // Update relasi mata pelajaran
        $teacher->subjects()->sync($request->subjects);

        return redirect()->route('admin.teacher.index')->with('success', 'Data Guru dan mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = User::where('role', 'pengajar')
                       ->where('created_by', auth()->user()->getInstitutionId())
                       ->findOrFail($id);
        
        if ($teacher->role !== 'pengajar') {
            abort(403);
        }

        $teacher->delete();

        return redirect()->route('admin.teacher.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function approve(string $id)
    {
        $teacher = User::where('role', 'pengajar')->where('created_by', auth()->user()->getInstitutionId())->findOrFail($id);
        if ($teacher->role !== 'pengajar') abort(403);
        
        $teacher->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Akun guru berhasil disetujui dan diaktifkan.');
    }

    public function suspend(string $id)
    {
        $teacher = User::where('role', 'pengajar')->where('created_by', auth()->user()->getInstitutionId())->findOrFail($id);
        if ($teacher->role !== 'pengajar') abort(403);
        
        $teacher->update(['status' => 'suspended']);
        return redirect()->back()->with('success', 'Akun guru berhasil dinonaktifkan (suspend).');
    }

    public function activate(string $id)
    {
        $teacher = User::where('role', 'pengajar')->where('created_by', auth()->user()->getInstitutionId())->findOrFail($id);
        if ($teacher->role !== 'pengajar') abort(403);
        
        $teacher->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Akun guru berhasil diaktifkan kembali.');
    }

    public function assignProctor(string $id)
    {
        $teacher = User::where('role', 'pengajar')->where('created_by', auth()->user()->getInstitutionId())->findOrFail($id);
        if ($teacher->role !== 'pengajar') abort(403);
        
        $institutionId = auth()->user()->getInstitutionId();
        
        // Fetch active exam sessions for this institution
        $sessions = \App\Models\ExamSession::where('created_by', $institutionId)
                        ->where('is_active', true)
                        ->orderBy('start_time', 'asc')
                        ->get();
                        
        // Fetch rooms for this institution
        $rooms = \App\Models\ExamRoom::where('created_by', $institutionId)->get();
        
        // Fetch current assignments for this teacher
        $assignments = $teacher->proctorAssignments()->get();

        return view('admin.teacher.assign_proctor', compact('teacher', 'sessions', 'rooms', 'assignments'));
    }

    public function storeProctor(Request $request, string $id)
    {
        $teacher = User::where('role', 'pengajar')->where('created_by', auth()->user()->getInstitutionId())->findOrFail($id);
        if ($teacher->role !== 'pengajar') abort(403);

        $request->validate([
            'assignments' => 'array',
            'assignments.*.exam_session_id' => 'required|exists:exam_sessions,id',
            'assignments.*.exam_room_id' => 'required|exists:exam_rooms,id',
        ]);

        // First detach all current assignments
        $teacher->proctorAssignments()->detach();

        // Attach new assignments if any
        if ($request->has('assignments') && is_array($request->assignments)) {
            foreach ($request->assignments as $assignment) {
                $teacher->proctorAssignments()->attach($assignment['exam_session_id'], ['exam_room_id' => $assignment['exam_room_id']]);
            }
        }

        return redirect()->route('admin.teacher.index')->with('success', 'Penugasan pengawas berhasil disimpan.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $importer = new TeachersImport();
            Excel::import($importer, $request->file('file'));
            
            $msg = "Import Selesai. {$importer->importedCount} guru baru berhasil ditambahkan.";
            
            // Warnings/Info
            if (count($importer->skippedErrors) > 0) {
                $msg .= " WARNING: " . implode('; ', $importer->skippedErrors);
            }

            if (count($importer->duplicates) > 0) {
                $duplicateList = implode(', ', array_slice($importer->duplicates, 0, 5));
                if (count($importer->duplicates) > 5) {
                    $duplicateList .= ", dan " . (count($importer->duplicates) - 5) . " lainnya";
                }
                $msg .= " INFO: " . count($importer->duplicates) . " data diperbarui: " . $duplicateList;
            }

            $alertType = (count($importer->skippedErrors) > 0) ? 'warning' : 'success';

            return redirect()->back()->with($alertType, $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TeacherTemplateExport, 'template_guru.xlsx');
    }
}
