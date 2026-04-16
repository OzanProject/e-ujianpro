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

        // Base Query
        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
            $query = Question::whereIn('subject_id', $subjects->pluck('id'))
                ->where(function($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhereNull('created_by'); // Tampilkan soal lama yang belum ditandai pemiliknya
                })->with('subject');
        } else {
            $subjects = Subject::where('created_by', auth()->id())->get();
            $query = Question::whereIn('subject_id', $subjects->pluck('id'))->with('subject');
        }

        // Global Statistics (Before filtering)
        $stats = [
            'total' => (clone $query)->count(),
            'mc' => (clone $query)->where('type', 'multiple_choice')->count(),
            'essay' => (clone $query)->where('type', 'essay')->count(),
            'easy' => (clone $query)->where('difficulty', 'easy')->count(),
            'medium' => (clone $query)->where('difficulty', 'medium')->count(),
            'hard' => (clone $query)->where('difficulty', 'hard')->count(),
        ];

        // Filtering
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
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        $questions = $query->with('tags')->latest()->paginate(10);

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
            // Ensure teacher can only see their own reading texts if needed, 
            // but for now let's keep it open or filter by subject
            $readingTexts = \App\Models\ReadingText::whereIn('subject_id', $subjects->pluck('id'))->get();
            $questionGroups = \App\Models\QuestionGroup::whereIn('subject_id', $subjects->pluck('id'))->get();
        } else {
            $subjects = Subject::where('created_by', auth()->id())->get();
            $readingTexts = \App\Models\ReadingText::all();
            $questionGroups = \App\Models\QuestionGroup::all();
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
            'type' => 'required|in:multiple_choice,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.content' => 'required_if:type,multiple_choice',
            'correct_option' => 'required_if:type,multiple_choice',
            'tags' => 'nullable|string', // Comma separated tags
        ]);

        try {
            DB::beginTransaction();

            // Auto-Handle Petunjuk / Reading Text
            $readingTextId = $request->reading_text_id;
            if ($request->filled('reading_text_content')) {
                $rt = \App\Models\ReadingText::create([
                    'subject_id' => $request->subject_id,
                    'code' => 'PET-MNL-' . time() . '-' . uniqid(),
                    'title' => 'Petunjuk Manual',
                    'content' => $request->input('reading_text_content')
                ]);
                $readingTextId = $rt->id;
            }

            $question = Question::create([
                'subject_id' => $request->subject_id,
                'reading_text_id' => $readingTextId,
                'question_group_id' => $request->question_group_id,
                'content' => $request->input('content'),
                'type' => $request->type,
                'difficulty' => $request->difficulty,
                'created_by' => auth()->id(),
            ]);

            // Save Options
            if ($request->type == 'multiple_choice') {
                foreach ($request->options as $index => $option) {
                    $question->options()->create([
                        'content' => $option['content'],
                        'is_correct' => $request->correct_option == $index
                    ]);
                }
            }

            // Save Tags
            if ($request->filled('tags')) {
                $tagNames = explode(',', $request->tags);
                $tagIds = [];
                foreach ($tagNames as $name) {
                    $tag = \App\Models\Tag::firstOrCreate(['name' => trim($name)]);
                    $tagIds[] = $tag->id;
                }
                $question->tags()->sync($tagIds);
            }

            DB::commit();

            $redirectRoute = auth()->user()->role === 'pengajar' ? 'pengajar.question.index' : 'admin.question.index';
            return redirect()->route($redirectRoute, ['subject_id' => $request->subject_id])
                ->with('success', 'Soal berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan soal: ' . $e->getMessage())->withInput();
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
        } else {
            $subjects = Subject::where('created_by', auth()->id())->get();
        }

        $readingTexts = \App\Models\ReadingText::all();
        $questionGroups = \App\Models\QuestionGroup::all();
        $question->load(['options', 'tags']);

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
            'type' => 'required|in:multiple_choice,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'tags' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Auto-Handle Petunjuk / Reading Text
            $readingTextId = $request->reading_text_id;
            if ($request->filled('reading_text_content')) {
                if ($question->readingText) {
                    $question->readingText->update([
                        'content' => $request->input('reading_text_content')
                    ]);
                    $readingTextId = $question->readingText->id;
                } else {
                    $rt = \App\Models\ReadingText::create([
                        'subject_id' => $request->subject_id,
                        'code' => 'PET-MNL-' . time() . '-' . uniqid(),
                        'title' => 'Petunjuk Manual',
                        'content' => $request->input('reading_text_content')
                    ]);
                    $readingTextId = $rt->id;
                }
            } elseif ($request->has('reading_text_content') && empty($request->input('reading_text_content'))) {
                // Jika input ada tapi dikosongi user, lepaskan petunjuk.
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

            // Handle Tags
            if ($request->has('tags')) {
                $tagNames = $request->filled('tags') ? array_map('trim', explode(',', $request->tags)) : [];
                $tagIds = [];
                foreach ($tagNames as $name) {
                    $tag = \App\Models\Tag::firstOrCreate(['name' => $name]);
                    $tagIds[] = $tag->id;
                }
                $question->tags()->sync($tagIds);
            }

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

            $redirectRoute = auth()->user()->role === 'pengajar' ? 'pengajar.question.index' : 'admin.question.index';
            return redirect()->route($redirectRoute, ['subject_id' => $request->subject_id])
                ->with('success', 'Soal berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui soal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Handle Image Upload from TinyMCE
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/questions', $filename, 'public');

            return response()->json([
                'location' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'Failed to upload image'], 500);
    }

    public function preview(Question $question)
    {
        $question->load('options', 'subject');
        
        $html = '<div class="question-preview-container p-2">';
        
        // Metadata Bar
        $html .= '<div class="d-flex align-items-center mb-4 mt-2 border-bottom pb-3">';
        $diffColor = $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger');
        $diffText = strtoupper($question->difficulty == 'easy' ? 'Mudah' : ($question->difficulty == 'medium' ? 'Sedang' : 'Sulit'));
        $html .= '<span class="badge badge-pill badge-light-' . $diffColor . ' text-' . $diffColor . ' px-3 py-1 mr-2 border border-' . $diffColor . '"><i class="fas fa-layer-group mr-1"></i> ' . $diffText . '</span>';
        $html .= '<span class="badge bg-light text-secondary border px-3 py-1"><i class="fas fa-book mr-1"></i> ' . ($question->subject->name ?? 'Mata Pelajaran') . '</span>';
        $html .= '</div>';

        // Reading Text / Petunjuk
        if ($question->readingText) {
            $html .= '<div class="alert alert-info border-left-info shadow-none bg-blue-50 mb-4 p-3 rounded-lg">';
            $html .= '<h6 class="alert-heading font-weight-bold text-info mb-2 text-sm text-uppercase"><i class="fas fa-info-circle mr-1"></i> Petunjuk / Bacaan</h6>';
            $html .= '<div class="text-dark bg-white p-3 border rounded shadow-sm" style="font-size: 0.95rem;">' . $question->readingText->content . '</div>';
            $html .= '</div>';
        }

        // Question Content
        $html .= '<div class="mb-4 text-dark font-weight-bold" style="font-size: 1.05rem; line-height: 1.8; font-family: Inter, sans-serif;">' . $question->content . '</div>';

        // Options or Essay block
        if ($question->type == 'multiple_choice') {
            $html .= '<div class="options-list mt-4">';
            $alphabet = range('A', 'E');
            foreach ($question->options as $index => $option) {
                if ($index >= 5) break; 
                $isCorrect = $option->is_correct;
                $html .= '<div class="d-flex align-items-start p-3 mb-3 rounded-lg border ' . ($isCorrect ? 'bg-success-fade border-success shadow-sm' : 'bg-white border-light shadow-xs') . '" style="transition: all 0.2s; ' . ($isCorrect ? 'border-width: 2px !important;' : '') . '">';
                $html .= '<span class="badge ' . ($isCorrect ? 'badge-success shadow-sm' : 'badge-light border') . ' mr-3 mt-1 py-2" style="width: 32px; font-size: 14px;">' . $alphabet[$index] . '</span>';
                $html .= '<div class="w-100 flex-grow-1" style="font-size: 0.95rem; line-height: 1.6;">' . $option->content . '</div>';
                if ($isCorrect) {
                    $html .= '<div class="ml-3"><i class="fas fa-check-circle text-success fa-lg mt-2"></i></div>';
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        } else {
            $html .= '<div class="p-5 bg-light-gray rounded-lg border text-muted text-center mt-4" style="border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important;">';
            $html .= '<div class="bg-white rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width:60px; height:60px;"><i class="fas fa-pen-nib fa-lg text-primary opacity-75"></i></div>';
            $html .= '<h6 class="font-weight-bold text-dark mb-1">Soal Uraian / Esai</h6>';
            $html .= '<p class="text-sm mb-0">Siswa akan diberikan area Teks Panjang untuk menjawab soal ini pada saat ujian.</p>';
            $html .= '</div>';
        }

        $html .= '</div>';
        
        // Injected Styles
        $html .= '<style>
            .bg-success-fade { background-color: #f0fdf4 !important; }
            .bg-light-gray { background-color: #f8f9fc !important; }
            .badge-light-success { background-color: rgba(40,167,69,0.1); border-color: rgba(40,167,69,0.2) !important; }
            .badge-light-warning { background-color: rgba(255,193,7,0.1); border-color: rgba(255,193,7,0.2) !important; }
            .badge-light-danger { background-color: rgba(220,53,69,0.1); border-color: rgba(220,53,69,0.2) !important; }
            .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.04); }
            
            /* Table Thumbnail Control */
            .reading-text-preview img, .question-preview img {
                max-height: 50px !important;
                width: auto !important;
                max-width: 100% !important;
                object-fit: contain;
                border-radius: 4px;
                border: 1px solid #e3e6f0;
                margin: 2px 0;
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
            'ids.*' => 'exists:questions,id'
        ]);

        try {
            Question::whereIn('id', $request->ids)->delete();
            return response()->json(['success' => true, 'message' => count($request->ids) . ' soal berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus soal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        try {
            if (auth()->user()->role === 'pengajar' && $question->created_by !== auth()->id()) {
                return back()->with('error', 'Anda tidak memiliki akses untuk menghapus soal ini.');
            }
            $question->delete();
            return back()->with('success', 'Soal berhasil dihapus.');
        } catch (\Exception $e) {
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
            $extension = $request->file('file')->getClientOriginalExtension();

            if (strtolower($extension) == 'docx') {
                // Enterprise Word Import
                $importer = new \App\Services\WordQuestionImporter($request->subject_id);
                $result = $importer->import($request->file('file')->path());

                $response = back()->with('success', "Import soal Word selesai. Berhasil: {$result['count']} soal.");

                if (!empty($result['errors'])) {
                    $response->with('error_list', $result['errors']);
                }

                return $response;
            } else {
                // Excel Import
                \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\QuestionsImport($request->subject_id), $request->file('file'));

                $redirectRoute = auth()->user()->role === 'pengajar' ? 'pengajar.question.index' : 'admin.question.index';
                return redirect()->route($redirectRoute, ['subject_id' => $request->subject_id])
                    ->with('success', 'Soal berhasil diimport dari Excel.');
            }
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

        // Professional Excel Export with Instructions and Styling
        return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths, \Maatwebsite\Excel\Concerns\WithTitle {
            public function collection()
            {
                return collect([
                    ['PETUNJUK PENGISIAN:'],
                    ['- Isi kolom "jenis" dengan: multiple_choice atau essay'],
                    ['- Kolom "jawaban" diisi (A/B/C/D/E) jika jenisnya multiple_choice'],
                    ['- Kolom "petunjuk_bacaan" bisa diisi teks petunjuk. (Khusus GAMBAR, lebih disarankan memakai template WORD)'],
                    ['- Kolom "tingkat_kesulitan" diisi: easy, medium, atau hard'],
                    [''], // Empty spacer
                    [
                        'Perhatikan teks berikut. Teks bacaan panjang...', // Petunjuk/Bacaan
                        'Contoh Soal Pilihan Ganda berdasarkan petunjuk diatas?',
                        'multiple_choice',
                        'Opsi A',
                        'Opsi B',
                        'Opsi C',
                        'Opsi D',
                        'Opsi E',
                        'A',
                        'easy'
                    ],
                    [
                        '', // No Petunjuk
                        'Contoh Soal Essay Bebas?',
                        'essay',
                        '', '', '', '', '', '',
                        'medium'
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
                    'tingkat_kesulitan'
                ];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                return [
                    7 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4e73df']]
                    ],
                    1 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1cc88a']]],
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 50,
                    'B' => 15,
                    'C' => 20,
                    'D' => 20,
                    'E' => 20,
                    'F' => 20,
                    'G' => 20,
                    'H' => 10,
                    'I' => 15,
                    'J' => 15,
                    'K' => 15,
                    'L' => 20
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
        // 🔥 WAJIB: Bersihkan semua output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop' => 600,
            'marginLeft' => 600,
            'marginRight' => 600,
            'marginBottom' => 600
        ]);

        $section->addText(
            'TEMPLATE IMPORT SOAL - E-UJIAN PRO',
            ['bold' => true, 'size' => 14],
            ['alignment' => 'center']
        );

        $section->addTextBreak(1);

        // Table
        $phpWord->addTableStyle('T1', [
            'borderSize' => 6,
            'borderColor' => '888888',
            'cellMargin' => 80
        ]);

        $table = $section->addTable('T1');

        $table->addRow();
        $table->addCell(3000)->addText('Petunjuk / Bacaan (Bisa Taruh Gambar Disini)', ['bold' => true]);
        $table->addCell(3000)->addText('Soal', ['bold' => true]);
        $table->addCell(1500)->addText('Jenis', ['bold' => true]);
        $table->addCell(1000)->addText('A', ['bold' => true]);
        $table->addCell(1000)->addText('B', ['bold' => true]);
        $table->addCell(1000)->addText('C', ['bold' => true]);
        $table->addCell(1000)->addText('D', ['bold' => true]);
        $table->addCell(1000)->addText('E', ['bold' => true]);
        $table->addCell(800)->addText('JWB', ['bold' => true]);

        // contoh
        $table->addRow();
        $table->addCell(3000)->addText('[Hapus] Taruh gambar bacaan disini (opsional)...');
        $table->addCell(3000)->addText('Contoh soal...');
        $table->addCell(1500)->addText('multiple_choice');
        $table->addCell(1000)->addText('A');
        $table->addCell(1000)->addText('B');
        $table->addCell(1000)->addText('C');
        $table->addCell(1000)->addText('D');
        $table->addCell(1000)->addText('E');
        $table->addCell(800)->addText('A');

        // 🔥 Simpan ke file temp (WAJIB)
        $fileName = 'Template_Soal.docx';
        $tempFile = storage_path('app/temp_' . time() . '.docx');

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        // 🔥 VALIDASI FILE ADA
        if (!file_exists($tempFile)) {
            abort(500, 'File gagal dibuat');
        }

        // 🔥 Download
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
