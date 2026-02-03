<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionGroup;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionGroupController extends Controller
{
    public function index()
    {
        $questionGroups = QuestionGroup::with('subject')->latest()->paginate(10);
        return view('admin.question_group.index', compact('questionGroups'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.question_group.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        QuestionGroup::create($request->all());

        return redirect()->route('admin.question_group.index')->with('success', 'Grup soal berhasil ditambahkan.');
    }

    public function edit(QuestionGroup $questionGroup)
    {
        $subjects = Subject::all();
        return view('admin.question_group.edit', compact('questionGroup', 'subjects'));
    }

    public function update(Request $request, QuestionGroup $questionGroup)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $questionGroup->update($request->all());

        return redirect()->route('admin.question_group.index')->with('success', 'Grup soal berhasil diperbarui.');
    }

    public function destroy(QuestionGroup $questionGroup)
    {
        $questionGroup->delete();
        return redirect()->back()->with('success', 'Grup soal berhasil dihapus.');
    }
}
