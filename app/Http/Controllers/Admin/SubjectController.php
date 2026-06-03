<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Scope to created_by
        $subjects = Subject::latest()->get();
        return view('admin.subject.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subject.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => [
                'required',
                'string',
                'max:10',
                \Illuminate\Validation\Rule::unique('subjects')->where(function ($query) {
                    return $query->where('created_by', auth()->user()->getInstitutionId());
                }),
            ],
            'name' => 'required|string|max:255',
            'kkm' => 'nullable|integer|min:0|max:100',
        ]);

        Subject::create([
            'code' => $request->code,
            'name' => $request->name,
            'kkm' => $request->kkm ?? 75,
        ]);

        return redirect()->route('admin.subject.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('admin.subject.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'code' => [
                'required',
                'string',
                'max:10',
                \Illuminate\Validation\Rule::unique('subjects')->where(function ($query) {
                    return $query->where('created_by', auth()->user()->getInstitutionId());
                })->ignore($subject->id),
            ],
            'name' => 'required|string|max:255',
            'kkm' => 'nullable|integer|min:0|max:100',
        ]);

        $data = $request->all();
        $data['kkm'] = $request->kkm ?? 75;
        $subject->update($data);

        return redirect()->route('admin.subject.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subject.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}

