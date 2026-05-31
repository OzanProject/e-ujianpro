<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

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

        $statsData = (clone $query)->withoutEagerLoads()->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN type = 'multiple_choice' THEN 1 ELSE 0 END) as mc,
            SUM(CASE WHEN type = 'multiple_choice_complex' THEN 1 ELSE 0 END) as mc_complex,
            SUM(CASE WHEN type = 'boolean_grid' THEN 1 ELSE 0 END) as boolean,
            SUM(CASE WHEN type = 'essay' THEN 1 ELSE 0 END) as essay,
            SUM(CASE WHEN difficulty = 'easy' THEN 1 ELSE 0 END) as easy,
            SUM(CASE WHEN difficulty = 'medium' THEN 1 ELSE 0 END) as medium,
            SUM(CASE WHEN difficulty = 'hard' THEN 1 ELSE 0 END) as hard
        ")->first();

        $stats = [
            'total' => $statsData->total ?? 0,
            'mc' => $statsData->mc ?? 0,
            'mc_complex' => $statsData->mc_complex ?? 0,
            'boolean' => $statsData->boolean ?? 0,
            'essay' => $statsData->essay ?? 0,
            'easy' => $statsData->easy ?? 0,
            'medium' => $statsData->medium ?? 0,
            'hard' => $statsData->hard ?? 0,
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
    public function store(StoreQuestionRequest $request)
    {
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
    public function update(UpdateQuestionRequest $request, Question $question)
    {
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

        return view('admin.question.preview', compact('question'));
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
    // Method untuk fitur Cetak Kartu Soal
    public function printCard(Request $request)
    {
        $user = auth()->user();
        $query = Question::query();

        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
            $query->whereIn('subject_id', $subjects->pluck('id'))
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)->orWhereNull('created_by');
                });
        } else {
            $subjects = Subject::where('created_by', $user->getInstitutionId())->get();
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

        $questions = $query->with(['subject', 'options', 'readingText', 'tags', 'creator'])->orderBy('id', 'asc')->get();
        $institution = $user->institution;

        return view('admin.question.print_card', compact('questions', 'institution'));
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
