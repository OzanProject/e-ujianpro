<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil user dengan role 'pengajar' yang dibuat oleh user yang login
        $teachers = User::where('role', 'pengajar')
                        ->where('created_by', auth()->id())
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
            'created_by' => auth()->id(), // Save Creator ID
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
        $teacher = User::findOrFail($id);
        
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
        $teacher = User::findOrFail($id);

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
        $teacher = User::findOrFail($id);
        
        if ($teacher->role !== 'pengajar') {
            abort(403);
        }

        $teacher->delete();

        return redirect()->route('admin.teacher.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function approve(string $id)
    {
        $teacher = User::findOrFail($id);
        if ($teacher->role !== 'pengajar') abort(403);
        
        $teacher->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Akun guru berhasil disetujui dan diaktifkan.');
    }

    public function suspend(string $id)
    {
        $teacher = User::findOrFail($id);
        if ($teacher->role !== 'pengajar') abort(403);
        
        $teacher->update(['status' => 'suspended']);
        return redirect()->back()->with('success', 'Akun guru berhasil dinonaktifkan (suspend).');
    }

    public function activate(string $id)
    {
        $teacher = User::findOrFail($id);
        if ($teacher->role !== 'pengajar') abort(403);
        
        $teacher->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Akun guru berhasil diaktifkan kembali.');
    }
}
