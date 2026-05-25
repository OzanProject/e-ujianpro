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
        // Global Scope via Multitenantable automatically filters by created_by
        $query = Student::with(['group', 'examRoom'])
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

        $perPage = $request->input('per_page', 10);
        $students = $query->paginate($perPage)->appends(request()->query());
        $groups = StudentGroup::all(); // Automatically scoped via Multitenantable
        $groups = StudentGroup::sortCollection($groups);
        $rooms = \App\Models\ExamRoom::all(); // Automatically scoped

        // Calculate Stats
        $statsRaw = Student::select('student_group_id', 'gender', \DB::raw('count(*) as count'))
            ->groupBy('student_group_id', 'gender')
            ->get();

        $groupStats = [];
        $totalMale = 0;
        $totalFemale = 0;

        foreach ($statsRaw as $stat) {
            $groupId = $stat->student_group_id ?? 0;
            if (!isset($groupStats[$groupId])) {
                $groupStats[$groupId] = ['L' => 0, 'P' => 0, 'name' => 'Tanpa Kelompok'];
            }
            
            $gender = strtoupper($stat->gender ?? '');
            if ($gender == 'L') {
                $groupStats[$groupId]['L'] = $stat->count;
                $totalMale += $stat->count;
            } elseif ($gender == 'P') {
                $groupStats[$groupId]['P'] = $stat->count;
                $totalFemale += $stat->count;
            }
        }

        // Map names
        foreach ($groups as $group) {
            if (isset($groupStats[$group->id])) {
                $groupStats[$group->id]['name'] = $group->name;
            }
        }

        return view('admin.student.index', compact('students', 'groups', 'rooms', 'groupStats', 'totalMale', 'totalFemale'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = StudentGroup::all();
        $rooms = \App\Models\ExamRoom::all();
        return view('admin.student.create', compact('groups', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:L,P',
            'nis' => [
                'required', 
                'string', 
                Rule::unique('students')->where(function ($query) {
                    return $query->where('created_by', auth()->id());
                })
            ], // Scoped Unique
            'nisn' => 'nullable|string',
            'participant_number' => 'nullable|string|max:50',
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
            'gender' => $request->gender,
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'participant_number' => $request->participant_number,
            'password' => Hash::make($request->password),
            'password_text' => $request->password,
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
        $student = Student::with('examRoom')->findOrFail($id);
        return view('admin.student.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        $groups = StudentGroup::all();
        $rooms = \App\Models\ExamRoom::all();
        return view('admin.student.edit', compact('student', 'groups', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:L,P',
            'nis' => [
                'required', 
                'string', 
                Rule::unique('students')->where(function ($query) {
                    return $query->where('created_by', auth()->id());
                })->ignore($student->id)
            ],
            'nisn' => 'nullable|string',
            'participant_number' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
            'kelas' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'student_group_id' => 'nullable|exists:student_groups,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'gender' => $request->gender,
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'participant_number' => $request->participant_number,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'student_group_id' => $request->student_group_id,
            'exam_room_id' => $request->exam_room_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['password_text'] = $request->password;
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
        // findOrFail is safe because of Multitenantable Global Scope
        $student = Student::findOrFail($id);
        
        // Delete associated User account
        if ($student->user) {
            $student->user->delete();
        }
        
        $student->delete();
        
        return redirect()->route('admin.student.index')->with('success', 'Peserta berhasil dihapus.');
    }

    public function deleteAll()
    {
        // Get all student IDs for this institution only (Multitenantable Scope is active)
        $students = Student::all();
        $userIds = $students->pluck('user_id')->filter();
        
        // Delete Students (Scoped)
        Student::query()->delete();
        
        // Delete Users (Scoped by IDs found)
        if ($userIds->isNotEmpty()) {
            \App\Models\User::whereIn('id', $userIds)->delete();
        }
        
        return redirect()->route('admin.student.index')->with('success', 'Semua data peserta di lembaga Anda berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'data_peserta_' . date('Ymd_His') . '.xlsx');
    }

    public function printCards()
    {
        $students = Student::with(['group', 'examRoom'])->get(); 
        $institution = \App\Models\Institution::where('user_id', (auth()->user()->role == 'admin_lembaga' ? auth()->id() : auth()->user()->created_by))->first();
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

    public function storePhotoAjax(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = $photo->getClientOriginalName();
            $nis = pathinfo($filename, PATHINFO_FILENAME);

            // Fetch student - Note: The Multitenantable trait automatically scopes this to the current institution
            $student = Student::where('nis', $nis)->first();

            if ($student) {
                // Resize image to max 500px width/height before saving if Intervention Image is available, 
                // but since we don't know, we just store it directly. One-by-one AJAX is already fast enough.
                $path = $photo->storeAs('student_photos', $student->id . '_' . time() . '.' . $photo->getClientOriginalExtension(), 'public');
                $student->update(['photo' => $path]);
                
                return response()->json([
                    'success' => true, 
                    'message' => "Foto $filename berhasil diupload.",
                    'nis' => $nis
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => "NIS $nis tidak ditemukan di data peserta.",
                'nis' => $nis
            ], 404);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada file yang diupload.'], 400);
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
        // Mencegah timeout di shared hosting (300 detik atau unlimited)
        set_time_limit(0);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'student_group_id' => 'nullable|exists:student_groups,id',
            'use_queue' => 'nullable|boolean'
        ]);

        try {
            // Cek apakah menggunakan queue (untuk file besar)
            $useQueue = $request->boolean('use_queue', false);
            
            if ($useQueue) {
                // Import dengan Queue (untuk file besar, tidak timeout)
                $importer = new \App\Imports\StudentsImportQueued($request->student_group_id);
                Excel::queueImport($importer, $request->file('file'));
                
                return redirect()->back()->with('success', 
                    'Import sedang diproses di background. Data akan muncul dalam beberapa menit. Refresh halaman untuk melihat hasilnya.');
            } else {
                // Import Synchronous (untuk file kecil < 50 siswa)
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
            }
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
