<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadingText;
use App\Models\Subject;
use Illuminate\Http\Request;

class ReadingTextController extends Controller
{
    public function index()
    {
        $readingTexts = ReadingText::with('subject')->latest()->paginate(10);
        return view('admin.reading_text.index', compact('readingTexts'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.reading_text.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        ReadingText::create($request->all());

        return redirect()->route('admin.reading_text.index')->with('success', 'Bacaan berhasil ditambahkan.');
    }

    public function show(ReadingText $readingText)
    {
        return view('admin.reading_text.show', compact('readingText'));
    }

    public function edit(ReadingText $readingText)
    {
        $subjects = Subject::all();
        return view('admin.reading_text.edit', compact('readingText', 'subjects'));
    }

    public function update(Request $request, ReadingText $readingText)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $readingText->update($request->all());

        return redirect()->route('admin.reading_text.index')->with('success', 'Bacaan berhasil diperbarui.');
    }

    public function destroy(ReadingText $readingText)
    {
        $readingText->delete();
        return redirect()->back()->with('success', 'Bacaan berhasil dihapus.');
    }
}
