<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionGroup;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionGroupController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.question_group' : 'admin.question_group';
    }

    public function index()
    {
        $user = auth()->user();
        $query = QuestionGroup::with('subject')->latest();

        if ($user->role === 'pengajar') {
            $query->whereIn('subject_id', $user->subjects->pluck('id'));
        } else {
            // Admin/Operator: Filter by their subjects
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjectIds = Subject::where('created_by', $creatorId)->pluck('id');
            $query->whereIn('subject_id', $subjectIds);
        }

        $questionGroups = $query->paginate(10);
        $baseRoute = $this->getBaseRoute();

        return view('admin.question_group.index', compact('questionGroups', 'baseRoute'));
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
        return view('admin.question_group.create', compact('subjects', 'baseRoute'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        // Security check
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        QuestionGroup::create([
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'created_by' => $user->id
        ]);

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Grup soal berhasil ditambahkan.');
    }

    public function edit(QuestionGroup $questionGroup)
    {
        $user = auth()->user();
        if ($user->role === 'pengajar') {
            if (!$user->subjects->contains($questionGroup->subject_id)) {
                abort(403, 'Akses Ditolak.');
            }
            $subjects = $user->subjects;
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = Subject::where('created_by', $creatorId)->get();
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.question_group.edit', compact('questionGroup', 'subjects', 'baseRoute'));
    }

    public function update(Request $request, QuestionGroup $questionGroup)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $questionGroup->update($request->all());

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Grup soal berhasil diperbarui.');
    }

    public function destroy(QuestionGroup $questionGroup)
    {
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($questionGroup->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $questionGroup->delete();
        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Grup soal berhasil dihapus.');
    }
    public function generate(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'strategy' => 'required|in:type,difficulty',
        ]);

        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $questions = \App\Models\Question::where('subject_id', $request->subject_id)->get();
        $strategy = $request->strategy;
        $createdCount = 0;
        $mappedCount = 0;

        $groupedQuestions = $questions->groupBy($strategy);

        foreach ($groupedQuestions as $key => $items) {
            if (empty($key)) continue;

            // Clean up name
            $name = ucwords(str_replace('_', ' ', $key));
            if ($strategy === 'type') {
                $typeMap = [
                    'multiple_choice' => 'Pilihan Ganda Tunggal',
                    'multiple_choice_complex' => 'Pilihan Ganda Kompleks',
                    'boolean_grid' => 'Benar / Salah',
                    'essay' => 'Esai / Uraian'
                ];
                $name = $typeMap[$key] ?? $name;
            }

            // Create or Find Group
            $group = QuestionGroup::firstOrCreate(
                [
                    'subject_id' => $request->subject_id,
                    'name' => $name,
                    'created_by' => $user->id
                ]
            );

            if ($group->wasRecentlyCreated) {
                $createdCount++;
            }

            // Map Questions
            foreach ($items as $question) {
                $question->update(['question_group_id' => $group->id]);
                $mappedCount++;
            }
        }

        return redirect()->route($this->getBaseRoute() . '.index')
            ->with('success', "Berhasil: $createdCount grup baru dibuat dan $mappedCount soal berhasil dikelompokkan secara otomatis.");
    }
}
