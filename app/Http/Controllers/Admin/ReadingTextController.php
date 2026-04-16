<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadingText;
use App\Models\Subject;
use Illuminate\Http\Request;

class ReadingTextController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.reading_text' : 'admin.reading_text';
    }

    public function index()
    {
        $user = auth()->user();
        $query = ReadingText::with('subject')->latest();

        if ($user->role === 'pengajar') {
            $query->whereIn('subject_id', $user->subjects->pluck('id'));
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjectIds = Subject::where('created_by', $creatorId)->pluck('id');
            $query->whereIn('subject_id', $subjectIds);
        }

        $readingTexts = $query->paginate(10);
        $baseRoute = $this->getBaseRoute();

        return view('admin.reading_text.index', compact('readingTexts', 'baseRoute'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = Subject::where('created_by', $creatorId)->get();
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.reading_text.create', compact('subjects', 'baseRoute'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $data = $request->all();
        $data['created_by'] = $user->id;
        ReadingText::create($data);

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Bacaan berhasil ditambahkan.');
    }

    public function show(ReadingText $readingText)
    {
        return view('admin.reading_text.show', compact('readingText'));
    }

    public function edit(ReadingText $readingText)
    {
        $user = auth()->user();
        if ($user->role === 'pengajar') {
            if (!$user->subjects->contains($readingText->subject_id)) {
                abort(403, 'Akses Ditolak.');
            }
            $subjects = $user->subjects;
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = Subject::where('created_by', $creatorId)->get();
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.reading_text.edit', compact('readingText', 'subjects', 'baseRoute'));
    }

    public function update(Request $request, ReadingText $readingText)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $readingText->update($request->all());

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Bacaan berhasil diperbarui.');
    }

    public function destroy(ReadingText $readingText)
    {
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($readingText->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $readingText->delete();
        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Bacaan berhasil dihapus.');
    }
}
