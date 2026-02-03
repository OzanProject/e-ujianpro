<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Scope to created_by (Institution)
        $query = Student::where('created_by', auth()->user()->id)
                        ->with(['group', 'examRoom'])
                        ->latest();

        // Filtering
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('nis', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('student_group_id')) {
            $query->where('student_group_id', $request->student_group_id);
        }
        
        if ($request->filled('exam_room_id')) {
            $value = $request->exam_room_id;
            if ($value == 'null') {
                $query->whereNull('exam_room_id');
            } else {
                $query->where('exam_room_id', $value);
            }
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $students = $query->paginate(10);
        $groups = StudentGroup::where('created_by', auth()->id())->get(); // Scope Groups
        $groups = StudentGroup::sortCollection($groups);
        $rooms = \App\Models\ExamRoom::where('created_by', auth()->id())->get();
        
        return view('admin.student.index', compact('students', 'groups', 'rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = StudentGroup::where('created_by', auth()->id())->get();
        $rooms = \App\Models\ExamRoom::where('created_by', auth()->id())->get();
        return view('admin.student.create', compact('groups', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => [
                'required', 
                'string', 
                Rule::unique('students')->where(function ($query) {
                    return $query->where('created_by', auth()->id());
                })
            ], // Scoped Unique
            'password' => 'required|string|min:6',
            'kelas' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'student_group_id' => 'nullable|exists:student_groups,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Check Quota
        if (!auth()->user()->canAddStudents(1)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambah peserta. Kuota siswa untuk lembaga Anda telah habis. Silakan hubungi Administrator untuk upgrade.');
        }

        $data = [
            'name' => $request->name,
            'nis' => $request->nis,
            'password' => Hash::make($request->password),
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'student_group_id' => $request->student_group_id,
            'exam_room_id' => $request->exam_room_id,
            'created_by' => auth()->id(), // Set Creator
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->storeAs('student_photos', $request->nis . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension(), 'public');
            $data['photo'] = $path;
        }

        Student::create($data);

        return redirect()->route('admin.student.index')->with('success', 'Peserta berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::where('created_by', auth()->id())->with('examRoom')->findOrFail($id);
        return view('admin.student.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::where('created_by', auth()->id())->findOrFail($id);
        $groups = StudentGroup::where('created_by', auth()->id())->get();
        $rooms = \App\Models\ExamRoom::where('created_by', auth()->id())->get();
        return view('admin.student.edit', compact('student', 'groups', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::where('created_by', auth()->id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => [
                'required', 
                'string', 
                Rule::unique('students')->where(function ($query) {
                    return $query->where('created_by', auth()->id());
                })->ignore($student->id)
            ],
            'password' => 'nullable|string|min:6',
            'kelas' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'student_group_id' => 'nullable|exists:student_groups,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'student_group_id' => $request->student_group_id,
            'exam_room_id' => $request->exam_room_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            
            $path = $request->file('photo')->storeAs('student_photos', $student->id . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension(), 'public');
            $data['photo'] = $path;
        }

        $student->update($data);

        return redirect()->route('admin.student.index')->with('success', 'Data peserta berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        
        // Delete associated User account to free up username/email and prevent zombies
        if ($student->user) {
            $student->user->delete();
        }
        
        $student->delete();
        
        return redirect()->route('admin.student.index')->with('success', 'Peserta berhasil dihapus.');
    }

    public function deleteAll()
    {
        // Get all student user IDs
        $userIds = Student::pluck('user_id');
        
        // Delete Students
        Student::query()->delete();
        
        // Delete Users
        \App\Models\User::whereIn('id', $userIds)->delete();
        
        return redirect()->route('admin.student.index')->with('success', 'Semua data peserta berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'data_peserta_' . date('Ymd_His') . '.xlsx');
    }

    public function printCards()
    {
        $students = Student::with(['group', 'examRoom'])->get(); 
        $institution = \App\Models\Institution::first();
        return view('admin.student.cards', compact('students', 'institution'));
    }

    public function uploadPhoto()
    {
        return view('admin.student.upload_photo');
    }

    public function storePhoto(Request $request)
    {
        $request->validate([
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photos')) {
            $successCount = 0;
            $failCount = 0;

            foreach ($request->file('photos') as $photo) {
                // Filename should be NIS.jpg or NIS.png
                $filename = $photo->getClientOriginalName();
                $nis = pathinfo($filename, PATHINFO_FILENAME); // Get filename without extension

                $student = Student::where('nis', $nis)->first();

                if ($student) {
                    $path = $photo->storeAs('student_photos', $student->id . '_' . time() . '.' . $photo->getClientOriginalExtension(), 'public');
                    $student->update(['photo' => $path]);
                    $successCount++;
                } else {
                    $failCount++;
                }
            }

            return redirect()->route('admin.student.index')->with('success', "$successCount foto berhasil diupload. $failCount gagal (NIS tidak ditemukan).");
        }

        return redirect()->back()->with('error', 'Tidak ada file yang dipilih.');
    }

    public function broadcastEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $students = Student::whereNotNull('email')->get();
        $count = 0;

        foreach ($students as $student) {
            \Illuminate\Support\Facades\Mail::to($student->email)->send(new \App\Mail\BroadcastEmail($request->subject, $request->message, $student));
            $count++;
        }

        return redirect()->back()->with('success', "Email berhasil dikirim ke $count peserta.");
    }

    public function broadcastWhatsapp(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $students = Student::whereNotNull('phone_number')->get();
        
        // Pass data to a view that lists WA links
        return view('admin.student.broadcast_whatsapp', compact('students', 'request'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'student_group_id' => 'nullable|exists:student_groups,id'
        ]);

        try {
            $importer = new \App\Imports\StudentsImport($request->student_group_id);
            Excel::import($importer, $request->file('file'));
            
            $msg = "Import Selesai. {$importer->importedCount} peserta baru berhasil ditambahkan.";
            
            // Warning for Quota Skip
            if ($importer->skippedCount > 0) {
                $msg .= " WARNING: {$importer->skippedCount} data DILEWATI karena kuota penuh.";
            }

            // Info for Duplicates
            if (count($importer->duplicates) > 0) {
                $duplicateList = implode(', ', array_slice($importer->duplicates, 0, 5)); // Show first 5
                if (count($importer->duplicates) > 5) {
                    $duplicateList .= ", dan " . (count($importer->duplicates) - 5) . " lainnya";
                }
                $msg .= " INFO: " . count($importer->duplicates) . " data memiliki NIS sama (Data diperbarui): " . $duplicateList;
            }

            // Determine Alert Type based on results
            $alertType = ($importer->skippedCount > 0) ? 'warning' : 'success';

            return redirect()->back()->with($alertType, $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(Request $request)
    {
        $filename = 'template_siswa.xlsx';
        
        if ($request->has('student_group_id') && $request->student_group_id) {
            $group = StudentGroup::find($request->student_group_id);
            if ($group) {
                // Sanitize filename
                $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $group->name);
                $filename = 'template_siswa_' . $cleanName . '.xlsx';
            }
        }

        return Excel::download(new \App\Exports\StudentTemplateExport, $filename);
    }
}
