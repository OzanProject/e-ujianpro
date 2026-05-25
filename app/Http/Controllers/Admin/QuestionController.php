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
            $query = Question::whereIn('subject_id', $subjects->pluck('id'))->with('subject');
        } else {
            $subjects = Subject::where('created_by', auth()->user()->getInstitutionId())->get();
            $query = Question::whereIn('subject_id', $subjects->pluck('id'))->with('subject');
        }

        $stats = [
            'total' => (clone $query)->count(),
            'mc' => (clone $query)->where('type', 'multiple_choice')->count(),
            'mc_complex' => (clone $query)->where('type', 'multiple_choice_complex')->count(),
            'boolean' => (clone $query)->where('type', 'boolean_grid')->count(),
            'essay' => (clone $query)->where('type', 'essay')->count(),
            'easy' => (clone $query)->where('difficulty', 'easy')->count(),
            'medium' => (clone $query)->where('difficulty', 'medium')->count(),
            'hard' => (clone $query)->where('difficulty', 'hard')->count(),
        ];

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('content', 'like', '%' . $request->search . '%')
                    ->orWhereHas('readingText', function ($rt) use ($request) {
                        $rt->where('content', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('options', function ($opt) use ($request) {
                        $opt->where('content', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $perPage = in_array((int) $request->get('per_page', 10), [10, 20, 50, 100])
            ? (int) $request->get('per_page', 10)
            : 10;

        $questions = $query
            ->with(['tags', 'readingText', 'options'])
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.question.index', compact('questions', 'subjects', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
            $readingTexts = \App\Models\ReadingText::whereIn('subject_id', $subjects->pluck('id'))->get();
            $questionGroups = \App\Models\QuestionGroup::whereIn('subject_id', $subjects->pluck('id'))->get();
        } else {
            $subjects = Subject::where('created_by', auth()->user()->getInstitutionId())->get();
            $readingTexts = \App\Models\ReadingText::whereIn('subject_id', $subjects->pluck('id'))->get();
            $questionGroups = \App\Models\QuestionGroup::whereIn('subject_id', $subjects->pluck('id'))->get();
        }

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
            'type' => 'required|in:multiple_choice,multiple_choice_complex,boolean_grid,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'options_single' => 'required_if:type,multiple_choice|array',
            'options_complex' => 'required_if:type,multiple_choice_complex|array',
            'options_grid' => 'required_if:type,boolean_grid|array',
            'tags' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $readingTextId = $request->reading_text_id;

            if ($request->filled('reading_text_content')) {
                $readingText = \App\Models\ReadingText::create([
                    'subject_id' => $request->subject_id,
                    'code' => 'PET-MNL-' . time() . '-' . uniqid(),
                    'title' => 'Petunjuk Manual',
                    'content' => $request->input('reading_text_content'),
                ]);

                $readingTextId = $readingText->id;
            }

            $question = Question::create([
                'subject_id' => $request->subject_id,
                'reading_text_id' => $readingTextId,
                'question_group_id' => $request->question_group_id,
                'content' => $request->input('content'),
                'type' => $request->type,
                'difficulty' => $request->difficulty,
                'created_by' => auth()->user()->getInstitutionId(),
            ]);

            if ($request->type === 'multiple_choice') {
                foreach ($request->options_single as $index => $option) {
                    $content = $option['content'] ?? '';

                    if ($this->hasContent($content)) {
                        $question->options()->create([
                            'content' => $content,
                            'is_correct' => (string) $request->correct_option === (string) $index,
                        ]);
                    }
                }
            } elseif ($request->type === 'multiple_choice_complex') {
                $correctOptions = array_map('strval', $request->correct_options_complex ?? []);

                foreach ($request->options_complex as $index => $option) {
                    $content = $option['content'] ?? '';

                    if ($this->hasContent($content)) {
                        $question->options()->create([
                            'content' => $content,
                            'is_correct' => in_array((string) $index, $correctOptions, true),
                        ]);
                    }
                }
            } elseif ($request->type === 'boolean_grid') {
                foreach ($request->options_grid as $index => $option) {
                    $content = $option['content'] ?? '';

                    if ($this->hasContent($content)) {
                        $question->options()->create([
                            'content' => $content,
                            'is_correct' => ($request->grid_correct[$index] ?? 1) == 1,
                        ]);
                    }
                }
            }

            $this->syncTags($question, $request->input('tags'));

            DB::commit();

            $redirectRoute = auth()->user()->role === 'pengajar'
                ? 'pengajar.question.index'
                : 'admin.question.index';

            return redirect()
                ->route($redirectRoute, ['subject_id' => $request->subject_id])
                ->with('success', 'Soal berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Gagal menyimpan soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $user = auth()->user();

        if ($user->role === 'pengajar') {
            if (!$user->subjects->contains($question->subject_id)) {
                abort(403, 'Akses Ditolak.');
            }

            $subjects = $user->subjects;
            $readingTexts = \App\Models\ReadingText::whereIn('subject_id', $subjects->pluck('id'))->get();
            $questionGroups = \App\Models\QuestionGroup::whereIn('subject_id', $subjects->pluck('id'))->get();
        } else {
            $subjects = Subject::where('created_by', auth()->user()->getInstitutionId())->get();
            $readingTexts = \App\Models\ReadingText::whereIn('subject_id', $subjects->pluck('id'))->get();
            $questionGroups = \App\Models\QuestionGroup::whereIn('subject_id', $subjects->pluck('id'))->get();
        }

        $question->load(['options', 'tags', 'readingText']);
        $tagsString = $question->tags->pluck('name')->implode(', ');

        return view('admin.question.edit', compact('question', 'subjects', 'readingTexts', 'questionGroups', 'tagsString'));
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
            'type' => 'required|in:multiple_choice,multiple_choice_complex,boolean_grid,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'tags' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $readingTextId = $request->reading_text_id;

            if ($request->filled('reading_text_content')) {
                if ($question->readingText) {
                    $question->readingText->update([
                        'subject_id' => $request->subject_id,
                        'content' => $request->input('reading_text_content'),
                    ]);

                    $readingTextId = $question->readingText->id;
                } else {
                    $readingText = \App\Models\ReadingText::create([
                        'subject_id' => $request->subject_id,
                        'code' => 'PET-MNL-' . time() . '-' . uniqid(),
                        'title' => 'Petunjuk Manual',
                        'content' => $request->input('reading_text_content'),
                    ]);

                    $readingTextId = $readingText->id;
                }
            } elseif ($request->has('reading_text_content') && empty($request->input('reading_text_content'))) {
                $readingTextId = null;
            }

            $question->update([
                'subject_id' => $request->subject_id,
                'reading_text_id' => $readingTextId,
                'question_group_id' => $request->question_group_id,
                'content' => $request->input('content'),
                'type' => $request->type,
                'difficulty' => $request->difficulty,
            ]);

            $this->syncTags($question, $request->input('tags'));

            $question->options()->delete();

            if ($request->type === 'multiple_choice') {
                foreach (($request->options_single ?? []) as $index => $option) {
                    $content = $option['content'] ?? '';

                    if ($this->hasContent($content)) {
                        $question->options()->create([
                            'content' => $content,
                            'is_correct' => (string) $request->correct_option === (string) $index,
                        ]);
                    }
                }
            } elseif ($request->type === 'multiple_choice_complex') {
                $correctOptions = array_map('strval', $request->correct_options_complex ?? []);

                foreach (($request->options_complex ?? []) as $index => $option) {
                    $content = $option['content'] ?? '';

                    if ($this->hasContent($content)) {
                        $question->options()->create([
                            'content' => $content,
                            'is_correct' => in_array((string) $index, $correctOptions, true),
                        ]);
                    }
                }
            } elseif ($request->type === 'boolean_grid') {
                foreach (($request->options_grid ?? []) as $index => $option) {
                    $content = $option['content'] ?? '';

                    if ($this->hasContent($content)) {
                        $question->options()->create([
                            'content' => $content,
                            'is_correct' => ($request->grid_correct[$index] ?? 1) == 1,
                        ]);
                    }
                }
            }

            DB::commit();

            $redirectRoute = auth()->user()->role === 'pengajar'
                ? 'pengajar.question.index'
                : 'admin.question.index';

            return redirect()
                ->route($redirectRoute, ['subject_id' => $request->subject_id])
                ->with('success', 'Soal berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Gagal memperbarui soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Handle Image Upload from TinyMCE.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/questions', $filename, 'public');

            return response()->json([
                'location' => asset('storage/' . $path),
            ]);
        }

        return response()->json(['error' => 'Failed to upload image'], 500);
    }

    public function preview(Question $question)
    {
        $question->load(['options', 'subject', 'readingText']);

        $html = '<div class="question-preview-container p-2">';

        $diffColor = $question->difficulty === 'easy' ? 'success' : ($question->difficulty === 'medium' ? 'warning' : 'danger');
        $diffText = strtoupper($question->difficulty === 'easy' ? 'Mudah' : ($question->difficulty === 'medium' ? 'Sedang' : 'Sulit'));

        $html .= '<div class="d-flex align-items-center mb-4 mt-2 border-bottom pb-3">';
        $html .= '<span class="badge badge-pill badge-light-' . $diffColor . ' text-' . $diffColor . ' px-3 py-1 mr-2 border border-' . $diffColor . '"><i class="fas fa-layer-group mr-1"></i> ' . $diffText . '</span>';
        $html .= '<span class="badge bg-light text-secondary border px-3 py-1"><i class="fas fa-book mr-1"></i> ' . e($question->subject->name ?? 'Mata Pelajaran') . '</span>';
        $html .= '</div>';

        if ($question->readingText) {
            $html .= '<div class="alert alert-info border-left-info shadow-none bg-blue-50 mb-4 p-3 rounded-lg">';
            $html .= '<h6 class="alert-heading font-weight-bold text-info mb-2 text-sm text-uppercase"><i class="fas fa-info-circle mr-1"></i> Petunjuk / Bacaan</h6>';
            $html .= '<div class="text-dark bg-white p-3 border rounded shadow-sm question-preview-html" style="font-size: 0.95rem;">' . $question->readingText->content . '</div>';
            $html .= '</div>';
        }

        $html .= '<div class="mb-4 text-dark font-weight-bold question-preview-html" style="font-size: 1.05rem; line-height: 1.8; font-family: Inter, sans-serif;">' . $question->content . '</div>';

        if ($question->type === 'multiple_choice' || $question->type === 'multiple_choice_complex') {
            $html .= '<div class="options-list mt-4">';
            $alphabet = range('A', 'Z');

            foreach ($question->options as $index => $option) {
                if ($index >= count($alphabet)) {
                    break;
                }

                $isCorrect = (bool) $option->is_correct;

                $html .= '<div class="d-flex align-items-start p-3 mb-3 rounded-lg border ' . ($isCorrect ? 'bg-success-fade border-success shadow-sm' : 'bg-white border-light shadow-xs') . '" style="transition: all 0.2s; ' . ($isCorrect ? 'border-width: 2px !important;' : '') . '">';
                $html .= '<span class="badge ' . ($isCorrect ? 'badge-success shadow-sm' : 'badge-light border') . ' mr-3 mt-1 py-2" style="width: 32px; font-size: 14px;">' . $alphabet[$index] . '</span>';
                $html .= '<div class="w-100 flex-grow-1 question-preview-html" style="font-size: 0.95rem; line-height: 1.6;">' . $option->content . '</div>';

                if ($isCorrect) {
                    $html .= '<div class="ml-3"><i class="fas fa-check-circle text-success fa-lg mt-2"></i></div>';
                }

                $html .= '</div>';
            }

            $html .= '</div>';
        } elseif ($question->type === 'boolean_grid') {
            $html .= '<div class="mt-4">';
            $html .= '<table class="table table-bordered bg-white shadow-sm">';
            $html .= '<thead class="bg-light-gray"><tr><th>Pernyataan</th><th class="text-center" width="100">Kunci Jawaban</th></tr></thead><tbody>';

            foreach ($question->options as $option) {
                $answer = $option->is_correct
                    ? '<span class="badge badge-success px-3 py-2">BENAR</span>'
                    : '<span class="badge badge-danger px-3 py-2">SALAH</span>';

                $html .= '<tr><td class="align-middle question-preview-html">' . $option->content . '</td><td class="text-center align-middle">' . $answer . '</td></tr>';
            }

            $html .= '</tbody></table></div>';
        } else {
            $html .= '<div class="p-5 bg-light-gray rounded-lg border text-muted text-center mt-4" style="border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important;">';
            $html .= '<div class="bg-white rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width:60px; height:60px;"><i class="fas fa-pen-nib fa-lg text-primary opacity-75"></i></div>';
            $html .= '<h6 class="font-weight-bold text-dark mb-1">Soal Uraian / Esai</h6>';
            $html .= '<p class="text-sm mb-0">Siswa akan diberikan area teks panjang untuk menjawab soal ini pada saat ujian.</p>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '<style>
            .bg-success-fade { background-color: #f0fdf4 !important; }
            .bg-light-gray { background-color: #f8f9fc !important; }
            .badge-light-success { background-color: rgba(40,167,69,0.1); border-color: rgba(40,167,69,0.2) !important; }
            .badge-light-warning { background-color: rgba(255,193,7,0.1); border-color: rgba(255,193,7,0.2) !important; }
            .badge-light-danger { background-color: rgba(220,53,69,0.1); border-color: rgba(220,53,69,0.2) !important; }
            .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.04); }
            .question-preview-html img {
                max-width: 100% !important;
                height: auto !important;
                object-fit: contain;
                border-radius: 6px;
                border: 1px solid #e3e6f0;
                margin: 8px 0;
            }
        </style>';

        return response($html);
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:questions,id',
        ]);

        try {
            Question::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' soal berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus soal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        try {
            if (auth()->user()->role === 'pengajar' && $question->created_by !== auth()->user()->getInstitutionId()) {
                return back()->with('error', 'Anda tidak memiliki akses untuk menghapus soal ini.');
            }

            $question->delete();

            return back()->with('success', 'Soal berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus soal: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:xlsx,xls,csv,docx',
        ]);

        $user = auth()->user();
        $hasAccess = false;

        if ($user->role === 'pengajar') {
            $hasAccess = $user->subjects->contains($request->subject_id);
        } else {
            $subject = Subject::find($request->subject_id);
            $hasAccess = $subject && $subject->created_by == $user->id;
        }

        if (!$hasAccess) {
            return back()->with('error', 'Akses Ditolak. Anda tidak bisa mengimport ke mata pelajaran ini.');
        }

        try {
            $extension = strtolower($request->file('file')->getClientOriginalExtension());

            if ($extension === 'docx') {
                $importer = new \App\Services\WordQuestionImporter($request->subject_id);
                $result = $importer->import($request->file('file')->path());

                if (!empty($result['errors'])) {
                    session()->flash('error_list', $result['errors']);
                }

                $redirectRoute = auth()->user()->role === 'pengajar'
                    ? 'pengajar.question.index'
                    : 'admin.question.index';

                return redirect()
                    ->route($redirectRoute, ['subject_id' => $request->subject_id])
                    ->with('success', "Import soal Word selesai. Berhasil: {$result['count']} soal.");
            }

            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\QuestionsImport($request->subject_id),
                $request->file('file')
            );

            $redirectRoute = auth()->user()->role === 'pengajar'
                ? 'pengajar.question.index'
                : 'admin.question.index';

            return redirect()
                ->route($redirectRoute, ['subject_id' => $request->subject_id])
                ->with('success', 'Soal berhasil diimport dari Excel.');
        } catch (\Throwable $e) {
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

        return \Maatwebsite\Excel\Facades\Excel::download(new class implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithStyles,
            \Maatwebsite\Excel\Concerns\WithColumnWidths,
            \Maatwebsite\Excel\Concerns\WithTitle {

            public function collection()
            {
                return collect([
                    ['PETUNJUK PENGISIAN:'],
                    ['- Isi kolom "jenis" dengan: multiple_choice, multiple_choice_complex, boolean_grid, atau essay'],
                    ['- Kolom "jawaban" diisi A/B/C/D/E jika jenis multiple_choice'],
                    ['- Kolom "jawaban" diisi A,C,D jika jenis multiple_choice_complex'],
                    ['- Jika jenis boolean_grid, isi pernyataan pada opsi_a dst, dan jawaban: B,S,B'],
                    ['- Kolom "petunjuk_bacaan" bisa diisi teks petunjuk. Untuk gambar, lebih disarankan memakai template Word.'],
                    ['- Kolom "tingkat_kesulitan" diisi: easy, medium, atau hard'],
                    [''],
                    [
                        'Perhatikan teks berikut. Teks bacaan panjang...',
                        'Contoh Soal Pilihan Ganda berdasarkan petunjuk di atas?',
                        'multiple_choice',
                        'Opsi A',
                        'Opsi B',
                        'Opsi C',
                        'Opsi D',
                        'Opsi E',
                        'A',
                        'easy',
                    ],
                    [
                        '',
                        'Contoh Soal Essay Bebas?',
                        'essay',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        'medium',
                    ],
                ]);
            }

            public function headings(): array
            {
                return [
                    'petunjuk_bacaan',
                    'soal',
                    'jenis',
                    'opsi_a',
                    'opsi_b',
                    'opsi_c',
                    'opsi_d',
                    'opsi_e',
                    'jawaban',
                    'tingkat_kesulitan',
                ];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                return [
                    7 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '4e73df'],
                        ],
                    ],
                    1 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1cc88a']]],
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 50,
                    'B' => 50,
                    'C' => 24,
                    'D' => 25,
                    'E' => 25,
                    'F' => 25,
                    'G' => 25,
                    'H' => 25,
                    'I' => 20,
                    'J' => 20,
                ];
            }

            public function title(): string
            {
                return 'Template Import Soal';
            }
        }, $fileName);
    }

    public function downloadTemplateWord()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $section = $phpWord->addSection([
            'marginTop' => 600,
            'marginLeft' => 800,
            'marginRight' => 800,
            'marginBottom' => 600,
        ]);

        $section->addText('TEMPLATE IMPORT SOAL - E-UJIAN PRO', ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        $section->addText('(Isi soal bebas, kunci jawaban ada di tabel akhir)', ['italic' => true, 'size' => 10], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $section->addText('ATURAN PENULISAN:', ['bold' => true]);
        $section->addText('1. Stimulus boleh berupa teks atau gambar, diletakkan sebelum nomor soal.');
        $section->addText('2. Soal ditulis dengan format: 1. Teks soal (angka + titik).');
        $section->addText('3. Opsi jawaban ditulis: A. teks opsi, B. teks opsi, dan seterusnya.');
        $section->addText('4. Untuk Benar/Salah, gunakan tabel Pernyataan | Benar | Salah.');
        $section->addText('5. Kunci jawaban diletakkan di akhir dokumen dengan tabel No | Kunci | Pengecoh | Level.');
        $section->addTextBreak(1);

        $section->addText('BAGIAN I: PILIHAN GANDA', ['bold' => true]);
        $section->addText('Stimulus contoh: Budi ingin membuat nasi goreng dengan langkah-langkah berurutan.');
        $section->addText('1. Dalam ilmu komputer, urutan langkah-langkah logis untuk menyelesaikan masalah disebut...');
        $section->addText('A. Algoritma');
        $section->addText('B. Pemrograman');
        $section->addText('C. Dekomposisi');
        $section->addText('D. Abstraksi');
        $section->addTextBreak(1);

        $section->addText('Stimulus contoh: Perhatikan aktivitas mencari buku di perpustakaan.');
        $section->addText('2. Algoritma yang lebih efisien adalah...');
        $section->addText('A. Mencari semua rak satu per satu');
        $section->addText('B. Mencari sesuai kategori');
        $section->addText('C. Menunggu petugas');
        $section->addText('D. Membuka semua buku');
        $section->addTextBreak(1);

        $section->addText('BAGIAN II: PILIHAN GANDA KOMPLEKS', ['bold' => true]);
        $section->addText('3. Ciri-ciri algoritma yang baik adalah...');
        $section->addText('[ ] A. Jelas dan tidak ambigu');
        $section->addText('[ ] B. Berjalan tanpa henti');
        $section->addText('[ ] C. Memiliki titik akhir');
        $section->addText('[ ] D. Harus rumit');
        $section->addTextBreak(1);

        $section->addText('BAGIAN III: BENAR ATAU SALAH', ['bold' => true]);
        $section->addText('4. Tentukan benar atau salah pernyataan berikut!');
        $section->addTextBreak(1);

        $phpWord->addTableStyle('GridTable', ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $gridTable = $section->addTable('GridTable');
        $gridTable->addRow();
        $gridTable->addCell(7000)->addText('Pernyataan', ['bold' => true], ['alignment' => 'center']);
        $gridTable->addCell(1500)->addText('Benar', ['bold' => true], ['alignment' => 'center']);
        $gridTable->addCell(1500)->addText('Salah', ['bold' => true], ['alignment' => 'center']);

        $gridStatements = [
            "Struktur IF - THEN - ELSE digunakan untuk pengambilan keputusan.",
            'Looping digunakan untuk menghentikan program.',
            'Algoritma yang baik memiliki titik akhir.',
            'Flowchart menggunakan simbol grafis.',
        ];

        foreach ($gridStatements as $statement) {
            $gridTable->addRow();
            $gridTable->addCell(7000)->addText($statement);
            $gridTable->addCell(1500)->addText('');
            $gridTable->addCell(1500)->addText('');
        }

        $section->addTextBreak(2);
        $section->addText('TABEL KUNCI JAWABAN', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $section->addText('(Letakkan tabel ini di halaman paling akhir dokumen)', ['italic' => true, 'size' => 9], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $phpWord->addTableStyle('KeyTable', ['borderSize' => 6, 'borderColor' => '444444', 'cellMargin' => 80]);
        $keyTable = $section->addTable('KeyTable');
        $keyTable->addRow();
        $keyTable->addCell(1000)->addText('No', ['bold' => true], ['alignment' => 'center']);
        $keyTable->addCell(2000)->addText('Kunci', ['bold' => true], ['alignment' => 'center']);
        $keyTable->addCell(2000)->addText('Pengecoh', ['bold' => true], ['alignment' => 'center']);
        $keyTable->addCell(2000)->addText('Level', ['bold' => true], ['alignment' => 'center']);

        $exampleKeys = [
            ['1', 'A', 'B,C,D', 'Mudah'],
            ['2', 'B', 'A,C,D', 'Sedang'],
            ['3', 'A,C', 'B,D', 'Sedang'],
            ['4', 'B,S,B,B', '-', 'Sulit'],
        ];

        foreach ($exampleKeys as $row) {
            $keyTable->addRow();
            $keyTable->addCell(1000)->addText($row[0], [], ['alignment' => 'center']);
            $keyTable->addCell(2000)->addText($row[1], [], ['alignment' => 'center']);
            $keyTable->addCell(2000)->addText($row[2]);
            $keyTable->addCell(2000)->addText($row[3], [], ['alignment' => 'center']);
        }

        $fileName = 'Template_Soal_EUjianPro.docx';
        $tempFile = storage_path('app/temp_' . time() . '.docx');

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function exportWord(Request $request)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        $user = auth()->user();
        $query = Question::query();

        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
            $query->whereIn('subject_id', $subjects->pluck('id'))
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)->orWhereNull('created_by');
                });
        } else {
            $subjects = Subject::where('created_by', auth()->user()->getInstitutionId())->get();
            $query->whereIn('subject_id', $subjects->pluck('id'));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('content', 'like', '%' . $request->search . '%')
                    ->orWhereHas('readingText', function ($rt) use ($request) {
                        $rt->where('content', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('options', function ($opt) use ($request) {
                        $opt->where('content', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $questions = $query
            ->with(['subject', 'options', 'readingText'])
            ->orderBy('id', 'asc')
            ->get();

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection([
            'marginTop' => 600,
            'marginLeft' => 800,
            'marginRight' => 800,
            'marginBottom' => 600,
        ]);

        $subjectLabel = 'Semua Mapel';
        if ($request->filled('subject_id')) {
            $subjectLabel = Subject::find($request->subject_id)->name ?? 'Mapel';
        }

        $section->addText('EXPORT BANK SOAL - ' . strtoupper($subjectLabel), ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        $section->addText('Tanggal Export: ' . date('d/m/Y H:i'), ['italic' => true, 'size' => 10], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $phpWord->addTableStyle('GridTable', ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);

        $keyData = [];

        foreach ($questions as $index => $question) {
            $number = $index + 1;

            if ($question->readingText) {
                $section->addText('Petunjuk / Bacaan:', ['bold' => true, 'italic' => true]);
                $this->addHtmlToWord($section, $question->readingText->content);
                $section->addTextBreak(1);
            }

            $section->addText($number . '. ', ['bold' => true]);
            $this->addHtmlToWord($section, $question->content);

            if ($question->type === 'multiple_choice' || $question->type === 'multiple_choice_complex') {
                $letters = ['A', 'B', 'C', 'D', 'E'];

                foreach ($question->options as $optionIndex => $option) {
                    $letter = $letters[$optionIndex] ?? '?';
                    $section->addText($letter . '. ', ['bold' => true]);
                    $this->addHtmlToWord($section, $option->content);
                }

                $correctLetters = [];
                foreach ($question->options as $optionIndex => $option) {
                    if ($option->is_correct) {
                        $correctLetters[] = $letters[$optionIndex] ?? '';
                    }
                }

                $keyData[] = [
                    'no' => $number,
                    'kunci' => implode(',', $correctLetters),
                    'level' => ucfirst($question->difficulty),
                ];
            } elseif ($question->type === 'boolean_grid') {
                $gridTable = $section->addTable('GridTable');
                $gridTable->addRow();
                $gridTable->addCell(7000)->addText('Pernyataan', ['bold' => true], ['alignment' => 'center']);
                $gridTable->addCell(1500)->addText('Benar', ['bold' => true], ['alignment' => 'center']);
                $gridTable->addCell(1500)->addText('Salah', ['bold' => true], ['alignment' => 'center']);

                $answerPattern = [];

                foreach ($question->options as $option) {
                    $gridTable->addRow();
                    $cell = $gridTable->addCell(7000);
                    $this->addHtmlToWord($cell, $option->content);
                    $gridTable->addCell(1500)->addText('');
                    $gridTable->addCell(1500)->addText('');
                    $answerPattern[] = $option->is_correct ? 'B' : 'S';
                }

                $keyData[] = [
                    'no' => $number,
                    'kunci' => implode(',', $answerPattern),
                    'level' => ucfirst($question->difficulty),
                ];
            } else {
                $keyData[] = [
                    'no' => $number,
                    'kunci' => '(ESAI)',
                    'level' => ucfirst($question->difficulty),
                ];
            }

            $section->addTextBreak(1);
        }

        $section->addPageBreak();
        $section->addText('TABEL KUNCI JAWABAN', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $phpWord->addTableStyle('KeyTable', ['borderSize' => 6, 'borderColor' => '444444', 'cellMargin' => 80]);
        $keyTable = $section->addTable('KeyTable');
        $keyTable->addRow();
        $keyTable->addCell(1000)->addText('No', ['bold' => true], ['alignment' => 'center']);
        $keyTable->addCell(4000)->addText('Kunci', ['bold' => true], ['alignment' => 'center']);
        $keyTable->addCell(2000)->addText('Level', ['bold' => true], ['alignment' => 'center']);

        foreach ($keyData as $row) {
            $keyTable->addRow();
            $keyTable->addCell(1000)->addText($row['no'], [], ['alignment' => 'center']);
            $keyTable->addCell(4000)->addText($row['kunci'], [], ['alignment' => 'center']);
            $keyTable->addCell(2000)->addText($row['level'], [], ['alignment' => 'center']);
        }

        $fileName = 'Export_Soal_' . str_replace(' ', '_', $subjectLabel) . '_' . date('Ymd_His') . '.docx';
        $tempFile = storage_path('app/export_' . time() . '.docx');

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Helper: cek konten teks/gambar.
     */
    private function hasContent(?string $content): bool
    {
        $content = (string) $content;

        return trim(strip_tags($content)) !== '' || str_contains($content, '<img');
    }

    /**
     * Helper: sinkronisasi tag.
     */
    private function syncTags(Question $question, ?string $tags): void
    {
        if ($tags === null) {
            return;
        }

        $tagNames = array_filter(array_map('trim', explode(',', $tags)));
        $tagIds = [];

        foreach ($tagNames as $name) {
            if ($name === '') {
                continue;
            }

            $tag = \App\Models\Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        $question->tags()->sync($tagIds);
    }

    /**
     * Helper: masukkan HTML ke Word.
     */
    private function addHtmlToWord($container, ?string $html): void
    {
        $html = (string) $html;

        if (trim($html) === '') {
            return;
        }

        $html = str_replace(['&nbsp;', '<br>', '<br/>', '<br />'], [' ', '<br/>', '<br/>', '<br/>'], $html);
        $html = preg_replace('/<p[^>]*>/i', '<p>', $html);

        try {
            \PhpOffice\PhpWord\Shared\Html::addHtml($container, $html, false, false);
        } catch (\Throwable $e) {
            $container->addText(strip_tags($html));
        }
    }
}
