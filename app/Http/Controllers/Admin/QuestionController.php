<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
            // Filter questions based on assigned subjects
            $query = Question::whereIn('subject_id', $subjects->pluck('id'))->with('subject');
        } else {
            // Admin: Only see subjects created by them
            $subjects = Subject::where('created_by', auth()->id())->get();
            $query = Question::whereIn('subject_id', $subjects->pluck('id'))->with('subject');
        }

        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        $questions = $query->latest()->paginate(10);

        return view('admin.question.index', compact('questions', 'subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
        } else {
            $subjects = Subject::where('created_by', auth()->id())->get();
        }

        $readingTexts = \App\Models\ReadingText::all();
        $questionGroups = \App\Models\QuestionGroup::all();
        return view('admin.question.create', compact('subjects', 'readingTexts', 'questionGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'reading_text_id' => 'nullable|exists:reading_texts,id',
            'question_group_id' => 'nullable|exists:question_groups,id',
            'content' => 'required',
            'type' => 'required|in:multiple_choice,essay',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.content' => 'required_if:type,multiple_choice',
            'correct_option' => 'required_if:type,multiple_choice',
        ]);

        try {
            DB::beginTransaction();

            $question = Question::create([
                'subject_id' => $request->subject_id,
                'reading_text_id' => $request->reading_text_id,
                'question_group_id' => $request->question_group_id,
                'content' => $request->input('content'),
                'type' => $request->type,
            ]);

            if ($request->type == 'multiple_choice') {
                foreach ($request->options as $key => $optionData) {
                    $question->options()->create([
                        'content' => $optionData['content'],
                        'is_correct' => $key == $request->correct_option,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.question.index', ['subject_id' => $request->subject_id])
                             ->with('success', 'Soal berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan soal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $user = auth()->user();
        if ($user->role === 'pengajar') {
            // Check if teacher has access to this question's subject
            if (!$user->subjects->contains($question->subject_id)) {
                abort(403, 'Akses Ditolak. Anda tidak mengampu mata pelajaran ini.');
            }
            $subjects = $user->subjects;
        } else {
            $subjects = Subject::where('created_by', auth()->id())->get();
        }

        $readingTexts = \App\Models\ReadingText::all();
        $questionGroups = \App\Models\QuestionGroup::all();
        $question->load('options');
        return view('admin.question.edit', compact('question', 'subjects', 'readingTexts', 'questionGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'reading_text_id' => 'nullable|exists:reading_texts,id',
            'question_group_id' => 'nullable|exists:question_groups,id',
            'content' => 'required',
            'type' => 'required|in:multiple_choice,essay',
             // Validation for options can be more complex on update, simplifying for now
        ]);

        try {
            DB::beginTransaction();

            $question->update([
                'subject_id' => $request->subject_id,
                'reading_text_id' => $request->reading_text_id,
                'question_group_id' => $request->question_group_id,
                'content' => $request->input('content'),
                'type' => $request->type,
            ]);

            // Replace options logic (simplest approach: delete and recreate for multiple choice)
            // Ideally should update existing IDs, but for MVP recreation is safer for correctness consistency
            if ($request->type == 'multiple_choice') {
                $question->options()->delete();
                foreach ($request->options as $key => $optionData) {
                    $question->options()->create([
                        'content' => $optionData['content'],
                        'is_correct' => $key == $request->correct_option,
                    ]);
                }
            } else {
                 $question->options()->delete();
            }

            DB::commit();

            return redirect()->route('admin.question.index', ['subject_id' => $request->subject_id])
                             ->with('success', 'Soal berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui soal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $user = auth()->user();
        $hasAccess = false;
        
        if ($user->role === 'pengajar') {
            $hasAccess = $user->subjects->contains($request->subject_id);
        } else {
             // Admin: Check if subject created by them
             $subject = Subject::find($request->subject_id);
             $hasAccess = $subject && $subject->created_by == $user->id;
        }

        if (!$hasAccess) {
             return back()->with('error', 'Akses Ditolak. Anda tidak bisa mengimport ke mata pelajaran ini.');
        }

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\QuestionsImport($request->subject_id), $request->file('file'));
            return back()->with('success', 'Import soal berhasil (Excel).');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(Request $request)
    {
        $subjectName = 'Umum';
        if ($request->has('subject_id')) {
            $subject = Subject::find($request->subject_id);
            if ($subject) {
                $subjectName = $subject->name;
            }
        }

        $fileName = 'Format Import Soal - ' . $subjectName . '.xlsx';

        // Generate Excel on the fly using a closure export
        return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function collection()
            {
                return collect([
                    [
                        'Contoh Soal Pilihan Ganda?', 
                        'multiple_choice', 
                        'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E', 
                        'A',
                        'RT-001', // Contoh Kode Bacaan
                        'Kategori Mudah' // Contoh Grup Soal
                    ],
                    [
                        'Contoh Soal Essay?', 
                        'essay', 
                        '', '', '', '', '', 
                        '',
                        '',
                        ''
                    ],
                ]);
            }

            public function headings(): array
            {
                return [
                    'soal', 
                    'jenis', 
                    'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 
                    'jawaban',
                    'kode_bacaan',
                    'grup_soal'
                ];
            }
        }, $fileName);
    }
}
