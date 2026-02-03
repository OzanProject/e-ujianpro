<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentGroup;
use Illuminate\Http\Request;

class StudentGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = StudentGroup::where('created_by', auth()->id())
            ->withCount('students')
            ->get();

        // Custom Sort for Roman Numerals (VII, VIII, IX, X, etc.)
        $groups = StudentGroup::sortCollection($groups);

        return view('admin.student_group.index', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        StudentGroup::create([
            'name' => $request->name,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Kelompok peserta berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentGroup $studentGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:student_groups,name,' . $studentGroup->id,
        ]);

        $studentGroup->update($request->all());

        return redirect()->back()->with('success', 'Kelompok peserta berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentGroup $studentGroup)
    {
        if ($studentGroup->students()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal hapus. Masih ada siswa di kelompok ini.');
        }
        
        $studentGroup->delete();
        return redirect()->back()->with('success', 'Kelompok peserta berhasil dihapus.');
    }
}
