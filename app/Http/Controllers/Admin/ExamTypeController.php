<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $examTypes = ExamType::where('created_by', auth()->id())
            ->latest()
            ->paginate(10);
            
        return view('admin.exam_type.index', compact('examTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.exam_type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ExamType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.exam_type.index')
            ->with('success', 'Jenis Ujian berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamType $examType)
    {
        if ($examType->created_by != auth()->id()) {
            abort(403);
        }
        return view('admin.exam_type.edit', compact('examType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamType $examType)
    {
        if ($examType->created_by != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $examType->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'), // Checkbox handling
        ]);

        return redirect()->route('admin.exam_type.index')
            ->with('success', 'Jenis Ujian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamType $examType)
    {
        if ($examType->created_by != auth()->id()) {
            abort(403);
        }

        $examType->delete();

        return redirect()->route('admin.exam_type.index')
            ->with('success', 'Jenis Ujian berhasil dihapus.');
    }
}
