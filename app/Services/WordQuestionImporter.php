<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ReadingText;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WordQuestionImporter
{
    protected int $subjectId;
    protected array $errors = [];
    protected array $answerMap = [];

    /**
     * Format form/sistem:
     * A = 0, B = 1, C = 2, D = 3, E = 4
     */
    protected array $letterIndexMap = [
        'A' => 0,
        'B' => 1,
        'C' => 2,
        'D' => 3,
        'E' => 4,
    ];

    public function __construct($subjectId)
    {
        $this->subjectId = (int) $subjectId;
    }

    public function import($filePath): array
    {
        $this->errors = [];
        $this->answerMap = [];

        // Sanitize unsupported images (EMF, WMF, TIFF) in docx ZIP before loading
        $this->sanitizeDocxImages($filePath);

        $phpWord = IOFactory::load($filePath);
        $rawLines = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    if ($this->isAnswerKeyTable($element)) {
                        $this->parseAnswerKeyTable($element);
                        continue;
                    }

                    if ($this->isEnterpriseTable($element)) {
                        $this->convertEnterpriseTableToLines($element, $rawLines);
                        continue;
                    }

                    if ($this->isBooleanGridTable($element)) {
                        $this->convertGridTableToLines($element, $rawLines);
                        continue;
                    }
                }

                $html = $this->extractHtmlFromElement($element);
                $this->pushHtmlAsLines($html, $rawLines);
            }
        }

        $questions = [];
        $currentQuestion = null;
        $pendingReading = '';

        foreach ($rawLines as $i => $line) {
            $line = trim((string) $line);
            $cleanLine = $this->cleanText($line);
            $hasImage = $this->containsImage($line);
            $nextCleanLine = $this->getNextCleanLine($rawLines, $i);

            if ($cleanLine === '' && !$hasImage) {
                continue;
            }

            if (!$hasImage && $this->shouldSkipLine($cleanLine)) {
                continue;
            }

            if (preg_match('/^KUNCI\s+JAWABAN/iu', $cleanLine)) {
                break;
            }

            /*
             * Deteksi nomor soal.
             * Support:
             * 1. Soal
             * 1.Soal
             *
             * Jangan batasi dengan !$hasImage, karena nomor 12 Bahasa Inggris
             * bisa terbaca satu area dengan gambar/infografis.
             */
            if (preg_match('/^(?:\(\s*(\d{1,3})\s*\)|(\d{1,3})[\.\)]?)\s*(.*)/u', $cleanLine, $questionMatches)) {
                $number = (int) ($questionMatches[1] ?: $questionMatches[2]);

                /*
                 * Pengaman agar angka aneh dari dokumen/gambar/style Word
                 * tidak dianggap sebagai nomor soal.
                 * Juga pastikan nomor soal berurutan atau masuk akal.
                 */
                if ($number > 100 || ($currentQuestion && $number < $currentQuestion['number'] && $number < 10)) {
                    if ($currentQuestion) {
                        if (empty($currentQuestion['options'])) {
                            $currentQuestion['content'] = $this->appendHtml($currentQuestion['content'], $line);
                        } else {
                            $lastOptionKey = array_key_last($currentQuestion['options']);

                            if ($lastOptionKey !== null) {
                                $currentQuestion['options'][$lastOptionKey]['content'] = $this->appendHtml(
                                    $currentQuestion['options'][$lastOptionKey]['content'],
                                    $line
                                );
                            }
                        }
                    } else {
                        $pendingReading = $this->appendHtml($pendingReading, $line);
                    }

                    continue;
                }

                if ($currentQuestion) {
                    $questions[] = $currentQuestion;
                }

                $content = preg_replace('/^(\s*<[^>]*>\s*)*(?:\(\d{1,3}\)|\d{1,3}[\.\)]?)\s*/iu', '', $line);
                $splitData = $this->splitQuestionAndInlineOptions($content);

                $answerData = $this->answerMap[$number] ?? [];
                $answer = $answerData['answer'] ?? '';

                $currentQuestion = [
                    'number' => $number,
                    'reading' => $pendingReading,
                    'content' => $splitData['content'],
                    'options' => $splitData['options'],
                    'answer' => $answer,
                    'difficulty' => $answerData['difficulty'] ?? 'easy',
                    'type' => $this->detectQuestionType($answer),
                ];

                $pendingReading = '';
                continue;
            }

            /*
             * Deteksi opsi:
             * A. Jawaban
             * A) Jawaban
             * [ ] A. Jawaban
             * A. OneB. TwoC. ThreeD. Four
             */
            if ($currentQuestion && preg_match('/^(?:\[\s*\]\s*)?(?:\(\s*([A-Ea-e])\s*\)|([A-Ea-e])[\.\):]?)\s*(.*)/iu', $cleanLine, $optionMatches)) {
                $inlineOptions = $this->extractInlineOptions($line);

                if (count($inlineOptions) >= 2) {
                    foreach ($inlineOptions as $optionIndex => $optionData) {
                        $currentQuestion['options'][$optionIndex] = $optionData;
                    }

                    continue;
                }

                $letter = strtoupper($optionMatches[1] ?: $optionMatches[2]);
                $optionIndex = $this->letterToIndex($letter);
                $optionContent = preg_replace('/^(\s*<[^>]*>\s*)*(?:\[\s*\]\s*)?(?:\([A-Ea-e]\)|[A-Ea-e][\.\):]?)\s*/iu', '', $line);

                $currentQuestion['options'][$optionIndex] = [
                    'content' => $optionContent,
                ];

                continue;
            }

            /*
             * Support opsi dari bullet/list Word.
             */
            if ($currentQuestion && !$hasImage && preg_match('/^\[LIST_ITEM_MARKER\]\s*(.*)/iu', $cleanLine)) {
                $optionIndex = count($currentQuestion['options']);

                if ($optionIndex <= 4) {
                    $optionContent = preg_replace('/^(\s*<[^>]*>\s*)*\[LIST_ITEM_MARKER\]\s*/iu', '', $line);

                    $currentQuestion['options'][$optionIndex] = [
                        'content' => $optionContent,
                    ];

                    continue;
                }
            }

            if ($currentQuestion && !$hasImage && preg_match('/^(?:Kunci|Jawaban|Answer|Key):\s*(.*)/iu', $cleanLine, $answerMatches)) {
                $answer = $this->normalizeAnswer($answerMatches[1]);
                $currentQuestion['answer'] = $answer;
                $currentQuestion['type'] = $this->detectQuestionType($answer);
                continue;
            }

            if ($currentQuestion && !$hasImage && preg_match('/^(?:Tingkat|Kesulitan|Level|Difficulty):\s*(.*)/iu', $cleanLine, $difficultyMatches)) {
                $currentQuestion['difficulty'] = $this->normalizeDifficulty($difficultyMatches[1]);
                continue;
            }

            if (!$currentQuestion) {
                $pendingReading = $this->appendHtml($pendingReading, $line);
                continue;
            }

            /*
             * Gambar di tengah soal jangan langsung memutus soal.
             * Ini penting untuk nomor 12 Bahasa Inggris.
             */
            if ($hasImage) {
                $currentQuestion['reading'] = $this->appendHtml($currentQuestion['reading'], $line);
                continue;
            }

            if (empty($currentQuestion['options'])) {
                $currentQuestion['content'] = $this->appendHtml($currentQuestion['content'], $line);
                continue;
            }

            if ($this->isStartOfNextStimulus($line, $cleanLine, $hasImage, $nextCleanLine)) {
                $questions[] = $currentQuestion;
                $currentQuestion = null;
                $pendingReading = $this->appendHtml('', $line);
                continue;
            }

            $lastOptionKey = array_key_last($currentQuestion['options']);
            if ($lastOptionKey !== null) {
                $currentQuestion['options'][$lastOptionKey]['content'] = $this->appendHtml(
                    $currentQuestion['options'][$lastOptionKey]['content'],
                    $line
                );
            }
        }

        if ($currentQuestion) {
            $questions[] = $currentQuestion;
        }

        $importedCount = 0;

        foreach ($questions as $index => $questionData) {
            $number = $questionData['number'] ?? ($index + 1);

            /*
             * Rescue aman:
             * Jika soal sudah terbentuk tapi opsi kosong, scan ulang rawLines
             * dari nomor soal tersebut sampai sebelum nomor berikutnya.
             *
             * Ini memperbaiki kasus Bahasa Inggris nomor 12:
             * pertanyaan + opsi terpisah oleh gambar/halaman.
             */
            if (empty($questionData['options'])) {
                $rescued = $this->rescueOptionsFromRawLines($rawLines, (int) $number);

                if (!empty($rescued['options'])) {
                    $questionData['options'] = $rescued['options'];

                    if (!empty(trim(strip_tags($rescued['content'] ?? '')))) {
                        $questionData['content'] = $rescued['content'];
                    }

                    if (!empty($rescued['reading'])) {
                        $questionData['reading'] = $this->appendHtml($questionData['reading'] ?? '', $rescued['reading']);
                    }
                }
            }

            if (isset($this->answerMap[$number])) {
                $questionData['answer'] = $questionData['answer'] ?: $this->answerMap[$number]['answer'];
                $questionData['difficulty'] = $this->answerMap[$number]['difficulty'] ?? $questionData['difficulty'];
                $questionData['type'] = $this->detectQuestionType($questionData['answer']);
            }

            $questionData = $this->normalizeQuestionToFormFormat($questionData);

            if (!$this->validatePlain($questionData, $index)) {
                continue;
            }

            try {
                $this->savePlain($questionData);
                $importedCount++;
            } catch (\Throwable $e) {
                $this->errors[] = 'Soal Ke-' . $number . ': Gagal simpan (' . $e->getMessage() . ')';
            }
        }

        return [
            'count' => $importedCount,
            'errors' => $this->errors,
        ];
    }

    private function pushHtmlAsLines(string $html, array &$rawLines): void
    {
        $html = trim($html);

        if ($html === '') {
            return;
        }

        /*
         * Jangan pecah nomor soal secara global.
         * Ini menjaga mapel lain tetap aman dan menghindari nomor palsu.
         */
        $parts = explode('<br>', $html);

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part !== '' || $this->containsImage($part)) {
                $rawLines[] = $part;
            }
        }
    }

    private function splitQuestionAndInlineOptions(string $html): array
    {
        $html = trim($html);

        if ($html === '') {
            return [
                'content' => '',
                'options' => [],
            ];
        }

        $options = $this->extractInlineOptions($html);

        /*
         * Kalau opsi kurang dari 2, jangan dipaksa pecah.
         * Ini mencegah kalimat biasa rusak.
         */
        if (count($options) < 2) {
            return [
                'content' => $html,
                'options' => [],
            ];
        }

        if (!preg_match('/(?:^|\s|[?!.:])(?:\(([A-Ea-e])\)|([A-Ea-e])[\.\):])\s*/u', $html, $firstMatch, PREG_OFFSET_CAPTURE)) {
            return [
                'content' => $html,
                'options' => [],
            ];
        }

        $firstOptionPos = $firstMatch[1][1] ?? null;

        if ($firstOptionPos === null) {
            return [
                'content' => $html,
                'options' => [],
            ];
        }

        $content = trim(substr($html, 0, $firstOptionPos));

        if ($content === '') {
            return [
                'content' => $html,
                'options' => [],
            ];
        }

        return [
            'content' => $content,
            'options' => $options,
        ];
    }

    private function extractInlineOptions(string $html): array
    {
        $options = [];

        /*
         * Membaca opsi:
         * A. Opsi
         * B. Opsi
         * A. OpsiB. OpsiC. Opsi
         *
         * Lookahead support:
         * - label opsi berikutnya
         * - nomor soal berikutnya
         * - akhir string
         */
        preg_match_all(
            '/(?:^|<br>|\s|(?<=[?!.:]))(?:\(([A-Ea-e])\)|([A-Ea-e])[\.\):])\s*(.*?)(?=(?:<br>|\s|(?<=[?!.:]))?(?:\([A-Ea-e]\)|[A-Ea-e][\.\):])\s*|(?:<br>|\s)?(?:\(\d{1,3}\)|\d{1,3}[\.\)])\s*|$)/uis',
            $html,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $letter = strtoupper(trim($match[1] ?? ''));
            $content = trim($match[2] ?? '');

            if ($letter === '' || $content === '') {
                continue;
            }

            $index = $this->letterToIndex($letter);

            $options[$index] = [
                'content' => $content,
            ];
        }

        ksort($options);

        return $options;
    }

    private function rescueOptionsFromRawLines(array $rawLines, int $questionNumber): array
    {
        $found = false;
        $contentParts = [];
        $readingParts = [];
        $options = [];

        foreach ($rawLines as $line) {
            $line = trim((string) $line);
            $cleanLine = $this->cleanText($line);
            $hasImage = $this->containsImage($line);

            if ($cleanLine === '' && !$hasImage) {
                continue;
            }

            /*
             * Mulai scan dari nomor soal yang dicari.
             * Support:
             * 12. Soal
             * 12.Soal
             */
            if (
                !$found
                && preg_match('/^(?:\(\s*' . preg_quote((string) $questionNumber, '/') . '\s*\)|' . preg_quote((string) $questionNumber, '/') . '[\.\)]?)\s*(.*)/u', $cleanLine)
            ) {
                $found = true;

                $questionLine = preg_replace(
                    '/^(\s*<[^>]*>\s*)*(?:\(' . preg_quote((string) $questionNumber, '/') . '\)|' . preg_quote((string) $questionNumber, '/') . '[\.\)]?)\s*/iu',
                    '',
                    $line
                );

                $split = $this->splitQuestionAndInlineOptions($questionLine);

                if (!empty(trim(strip_tags($split['content'] ?? '')))) {
                    $contentParts[] = $split['content'];
                }

                foreach (($split['options'] ?? []) as $optionIndex => $optionData) {
                    $options[$optionIndex] = $optionData;
                }

                continue;
            }

            if (!$found) {
                continue;
            }

            /*
             * Berhenti ketika menemukan nomor soal berikutnya.
             */
            if (!$hasImage && $this->isNumberedQuestionLine($cleanLine, $questionNumber)) {
                break;
            }

            if (!$hasImage && $this->shouldSkipLine($cleanLine)) {
                continue;
            }

            if ($hasImage) {
                $readingParts[] = $line;
                continue;
            }

            /*
             * Ambil opsi A-E yang mungkin muncul sebelum/sesudah gambar.
             */
            if (preg_match('/^(?:\[\s*\]\s*)?(?:\(\s*([A-Ea-e])\s*\)|([A-Ea-e])[\.\):]?)\s*(.*)/iu', $cleanLine, $optionMatches)) {
                $inlineOptions = $this->extractInlineOptions($line);

                if (count($inlineOptions) >= 2) {
                    foreach ($inlineOptions as $optionIndex => $optionData) {
                        $options[$optionIndex] = $optionData;
                    }
                } else {
                    $letter = strtoupper($optionMatches[1] ?: $optionMatches[2]);
                    $optionIndex = $this->letterToIndex($letter);
                    $optionContent = preg_replace('/^(\s*<[^>]*>\s*)*(?:\[\s*\]\s*)?(?:\([A-Ea-e]\)|[A-Ea-e][\.\):]?)\s*/iu', '', $line);

                    $options[$optionIndex] = [
                        'content' => $optionContent,
                    ];
                }

                continue;
            }

            /*
             * Kalau belum ada opsi, baris masih dianggap bagian pertanyaan.
             * Kalau opsi sudah ada, baris tambahan ditempel ke opsi terakhir.
             */
            if (empty($options)) {
                $contentParts[] = $line;
            } else {
                $lastOptionKey = array_key_last($options);

                if ($lastOptionKey !== null) {
                    $options[$lastOptionKey]['content'] = $this->appendHtml(
                        $options[$lastOptionKey]['content'],
                        $line
                    );
                }
            }
        }

        ksort($options);

        return [
            'content' => implode('<br>', array_filter($contentParts)),
            'reading' => implode('<br>', array_filter($readingParts)),
            'options' => $options,
        ];
    }

    private function appendHtml(?string $base, ?string $addition): string
    {
        $base = trim((string) $base);
        $addition = trim((string) $addition);

        if ($addition === '') {
            return $base;
        }

        if ($base === '') {
            return $addition;
        }

        return $base . '<br>' . $addition;
    }

    private function isStartOfNextStimulus(string $htmlLine, string $cleanLine, bool $hasImage, ?string $nextCleanLine = null): bool
    {
        /*
         * Gambar tidak langsung dianggap stimulus soal berikutnya.
         */
        if ($hasImage) {
            return $nextCleanLine && $this->isNumberedQuestionLine($nextCleanLine, $this->getCurrentQuestionNumberFromHtml($htmlLine));
        }

        if ($cleanLine === '') {
            return false;
        }

        $upper = strtoupper(trim($cleanLine));

        foreach (['KUNCI:', 'JAWABAN:', 'ANSWER:', 'KEY:', 'TINGKAT:', 'KESULITAN:', 'LEVEL:', 'DIFFICULTY:'] as $prefix) {
            if (str_starts_with($upper, $prefix)) {
                return false;
            }
        }

        $currentNum = $this->getCurrentQuestionNumberFromHtml($htmlLine);

        if ($nextCleanLine && $this->isNumberedQuestionLine($nextCleanLine, $currentNum)) {
            return true;
        }

        $words = preg_split('/\s+/u', trim($cleanLine));

        // Jika ada banyak kata dan baris berikutnya adalah nomor soal, baru anggap stimulus
        return count(array_filter($words)) >= 10 && $nextCleanLine && $this->isNumberedQuestionLine($nextCleanLine, $currentNum);
    }

    private function normalizeQuestionToFormFormat(array $data): array
    {
        ksort($data['options']);

        $answerIndexes = $this->answerToIndexes($data['answer'] ?? '');

        if ($data['type'] === 'multiple_choice') {
            $data['correct_option'] = $answerIndexes[0] ?? 0;
            $data['options_single'] = $data['options'];
        }

        if ($data['type'] === 'multiple_choice_complex') {
            $data['correct_options_complex'] = $answerIndexes;
            $data['options_complex'] = $data['options'];
        }

        if ($data['type'] === 'boolean_grid') {
            $booleanValues = $this->answerToBooleanValues($data['answer'] ?? '');
            $gridCorrect = [];

            foreach ($data['options'] as $index => $option) {
                $gridCorrect[$index] = array_key_exists($index, $booleanValues)
                    ? $booleanValues[$index]
                    : null;
            }

            $data['grid_correct'] = $gridCorrect;
            $data['options_grid'] = $data['options'];
        }

        return $data;
    }

    private function savePlain(array $data): void
    {
        DB::transaction(function () use ($data) {
            $readingTextId = null;

            if (!empty(trim(strip_tags($data['reading'] ?? ''))) || $this->containsImage($data['reading'] ?? '')) {
                $readingText = ReadingText::create([
                    'subject_id' => $this->subjectId,
                    'code' => 'PET-' . time() . '-' . uniqid(),
                    'title' => 'Petunjuk Soal Otomatis',
                    'content' => $data['reading'],
                ]);

                $readingTextId = $readingText->id;
            }

            $question = Question::create([
                'subject_id' => $this->subjectId,
                'reading_text_id' => $readingTextId,
                'question_group_id' => null,
                'content' => $data['content'],
                'type' => $data['type'],
                'difficulty' => $data['difficulty'] ?? 'easy',
                'created_by' => auth()->id(),
            ]);

            if ($data['type'] === 'multiple_choice') {
                $correctIndex = (int) ($data['correct_option'] ?? 0);

                foreach (($data['options_single'] ?? []) as $index => $option) {
                    $content = $option['content'] ?? '';

                    if (trim(strip_tags($content)) === '' && !$this->containsImage($content)) {
                        continue;
                    }

                    QuestionOption::create([
                        'question_id' => $question->id,
                        'content' => $content,
                        'is_correct' => (int) $index === $correctIndex,
                    ]);
                }

                return;
            }

            if ($data['type'] === 'multiple_choice_complex') {
                $correctIndexes = array_map('intval', $data['correct_options_complex'] ?? []);

                foreach (($data['options_complex'] ?? []) as $index => $option) {
                    $content = $option['content'] ?? '';

                    if (trim(strip_tags($content)) === '' && !$this->containsImage($content)) {
                        continue;
                    }

                    QuestionOption::create([
                        'question_id' => $question->id,
                        'content' => $content,
                        'is_correct' => in_array((int) $index, $correctIndexes, true),
                    ]);
                }

                return;
            }

            if ($data['type'] === 'boolean_grid') {
                foreach (($data['options_grid'] ?? []) as $index => $option) {
                    $content = $option['content'] ?? '';

                    if (trim(strip_tags($content)) === '' && !$this->containsImage($content)) {
                        continue;
                    }

                    if (!array_key_exists($index, $data['grid_correct']) || $data['grid_correct'][$index] === null) {
                        throw new \RuntimeException('Kunci Benar/Salah tidak lengkap pada baris ' . ((int) $index + 1));
                    }

                    QuestionOption::create([
                        'question_id' => $question->id,
                        'content' => $content,
                        'is_correct' => (int) $data['grid_correct'][$index] === 1,
                    ]);
                }
            }
        });
    }

    private function validatePlain(array $data, int $index): bool
    {
        $number = $data['number'] ?? ($index + 1);

        if (empty(trim(strip_tags($data['content'] ?? ''))) && !$this->containsImage($data['content'] ?? '')) {
            $this->errors[] = "Soal Ke-$number: Konten soal kosong.";
            return false;
        }

        if (($data['type'] ?? '') !== 'essay' && empty($data['options'])) {
            $this->errors[] = "Soal Ke-$number: Tidak ada pilihan jawaban yang ditemukan.";
            return false;
        }

        if (($data['type'] ?? '') !== 'essay' && count($data['options']) < 2) {
            $this->errors[] = "Soal Ke-$number: Minimal harus ada 2 opsi.";
            return false;
        }

        if (($data['type'] ?? '') !== 'essay' && empty($data['answer'])) {
            $this->errors[] = "Soal Ke-$number: Kunci jawaban tidak ditemukan. Pastikan tabel kunci akhir tersedia atau ada baris Kunci:.";
            return false;
        }

        if ($data['type'] === 'multiple_choice_complex' && empty($data['correct_options_complex'])) {
            $this->errors[] = "Soal Ke-$number: Pilihan ganda kompleks belum memiliki jawaban benar.";
            return false;
        }

        if (($data['type'] ?? '') === 'boolean_grid') {
            $booleanValues = $this->answerToBooleanValues($data['answer'] ?? '');

            if (count($booleanValues) < count($data['options'])) {
                $this->errors[] = "Soal Ke-$number: Jumlah kunci Benar/Salah tidak sesuai jumlah pernyataan. Kunci terbaca: " . ($data['answer'] ?? '-');
                return false;
            }

            foreach ($data['options'] as $optionIndex => $option) {
                if (!array_key_exists($optionIndex, $booleanValues)) {
                    $this->errors[] = "Soal Ke-$number: Kunci Benar/Salah baris ke-" . ((int) $optionIndex + 1) . " tidak ditemukan.";
                    return false;
                }
            }
        }

        return true;
    }

    private function containsImage(string $html): bool
    {
        return stripos($html, '<img') !== false
            || stripos($html, '[Gambar') !== false
            || stripos($html, '[Gagal Gambar]') !== false
            || stripos($html, '[Gambar Kosong]') !== false;
    }

    private function getNextCleanLine(array $rawLines, int $currentIndex): ?string
    {
        $total = count($rawLines);

        for ($i = $currentIndex + 1; $i < $total; $i++) {
            $line = $rawLines[$i] ?? '';
            $clean = $this->cleanText($line);

            if ($clean !== '' && !$this->shouldSkipLine($clean)) {
                return $clean;
            }
        }

        return null;
    }

    private function isNumberedQuestionLine(string $line, int $currentNumber = 0): bool
    {
        $line = trim($this->cleanText($line));

        if (!preg_match('/^(?:\(\d{1,3}\)|\d{1,3}[\.\)])\s*/u', $line, $matches)) {
            return false;
        }

        preg_match('/\d+/', $matches[0], $numMatches);
        $number = (int) $numMatches[0];

        // Jika sedang memproses soal N, maka nomor soal berikutnya harus > N
        // Ini mencegah list item 1, 2, 3 di dalam stimulus dianggap soal baru
        if ($currentNumber > 0 && $number <= $currentNumber && $number < 10) {
            return false;
        }

        return $number > 0 && $number <= 100;
    }

    private function getCurrentQuestionNumberFromHtml(string $html): int
    {
        if (preg_match('/^(\s*<[^>]*>\s*)*(?:\(\s*(\d{1,3})\s*\)|(\d{1,3})[\.\)])/iu', $html, $matches)) {
            return (int) ($matches[2] ?: $matches[3]);
        }
        return 0;
    }

    private function shouldSkipLine(string $line): bool
    {
        return preg_match('/^BAGIAN\s/iu', $line)
            || preg_match('/^ASESMEN\s+SUMATIF/iu', $line)
            || preg_match('/^Tahun\s+Pelajaran/iu', $line)
            || preg_match('/^Mata\s+Pelajaran\s*:/iu', $line)
            || preg_match('/^Sekolah\s*:/iu', $line)
            || preg_match('/^Guru\s+Mapel\s*:/iu', $line)
            || preg_match('/^Kelas\s*:/iu', $line)
            || preg_match('/^Semester\s*:/iu', $line)
            || preg_match('/^Berdoalah\s+sebelum/iu', $line)
            || preg_match('/^Read\s+the\s+text\s+carefully/iu', $line)
            || preg_match('/^Read\s+the\s+text\s+below/iu', $line)
            || preg_match('/^Read\s+following\s+the\s+text\s+below/iu', $line)
            || preg_match('/^Look\s+at\s+picture\s+and\s+answer/iu', $line)
            || preg_match('/^Question\s+number\s+\d+/iu', $line)
            || preg_match('/^KUNCI\s+JAWABAN/iu', $line);
    }

    private function cleanText(?string $html): string
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }

    private function normalizeAnswer(?string $answer): string
    {
        $answer = strtoupper(trim((string) $answer));

        $answer = str_replace(
            ["\xc2\xa0", '&nbsp;', '；', ';', '/', '|', '，'],
            [' ', ' ', ',', ',', ',', ',', ','],
            $answer
        );

        $answer = preg_replace('/\s+/u', ' ', $answer);
        $answer = trim($answer);

        if (
            preg_match_all('/\d+\s*[\.]\s*([BSTF])/iu', $answer, $matches)
            || preg_match_all('/\d+\s*[\)]\s*([BSTF])/iu', $answer, $matches)
        ) {
            return implode(',', array_map('strtoupper', $matches[1]));
        }

        $compact = preg_replace('/\s+/u', '', $answer);

        // 1. Cek apakah ini format boolean grid (B,S,B atau TRUE,FALSE,TRUE)
        $booleanParts = explode(',', $answer);
        $isBooleanList = true;
        $normalizedBooleans = [];
        
        foreach ($booleanParts as $part) {
            $p = trim($part);
            if (preg_match('/^(TRUE|FALSE|BENAR|SALAH|T|F|B|S)$/iu', $p)) {
                $normalizedBooleans[] = strtoupper(substr($p, 0, 1));
            } else {
                $isBooleanList = false;
                break;
            }
        }

        if ($isBooleanList && count($normalizedBooleans) > 0) {
            return implode(',', $normalizedBooleans);
        }

        // 2. Cek format single letters (A,B,C)
        if (preg_match('/^[A-E](,[A-E])*$/iu', $compact)) {
            return strtoupper($compact);
        }

        // 3. Extract letters A-E (Hanya jika bukan format boolean di atas)
        if (preg_match_all('/[A-E]/iu', $compact, $matches)) {
            // Pastikan tidak merusak kata-kata seperti TRUE/FALSE yang tersisa
            return implode(',', array_map('strtoupper', $matches[0]));
        }

        return strtoupper($compact);
    }

    private function detectQuestionType(?string $answer): string
    {
        $answer = $this->normalizeAnswer($answer);

        if ($answer === '') {
            return 'multiple_choice';
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', strtoupper($answer)))));

        if (count($parts) > 1) {
            $onlyBoolean = true;

            foreach ($parts as $part) {
                if (!in_array($part, ['B', 'S', 'T', 'F', 'TRUE', 'FALSE', 'BENAR', 'SALAH'], true)) {
                    $onlyBoolean = false;
                    break;
                }
            }

            return $onlyBoolean ? 'boolean_grid' : 'multiple_choice_complex';
        }

        return 'multiple_choice';
    }

    private function normalizeDifficulty(?string $difficulty): string
    {
        $difficulty = strtolower(trim((string) $difficulty));

        if (str_contains($difficulty, 'sulit') || str_contains($difficulty, 'hard')) {
            return 'hard';
        }

        if (str_contains($difficulty, 'sedang') || str_contains($difficulty, 'medium')) {
            return 'medium';
        }

        return 'easy';
    }

    private function letterToIndex(string $letter): int
    {
        return $this->letterIndexMap[strtoupper($letter)] ?? 0;
    }

    private function answerToIndexes(?string $answer): array
    {
        $answer = $this->normalizeAnswer($answer);
        $parts = array_values(array_filter(array_map('trim', explode(',', strtoupper($answer)))));
        $indexes = [];

        foreach ($parts as $part) {
            if (isset($this->letterIndexMap[$part])) {
                $indexes[] = $this->letterIndexMap[$part];
            }
        }

        return array_values(array_unique($indexes));
    }

    private function answerToBooleanValues(?string $answer): array
    {
        $answer = $this->normalizeAnswer($answer);
        $parts = array_values(array_filter(array_map('trim', explode(',', strtoupper($answer)))));
        $values = [];

        foreach ($parts as $index => $part) {
            if (in_array($part, ['B', 'T', 'TRUE', 'BENAR'], true)) {
                $values[$index] = 1;
            } elseif (in_array($part, ['S', 'F', 'FALSE', 'SALAH'], true)) {
                $values[$index] = 0;
            }
        }

        return $values;
    }

    private function isAnswerKeyTable($table): bool
    {
        $rows = $table->getRows();

        if (count($rows) < 2) {
            return false;
        }

        $firstRowCells = $rows[0]->getCells();

        if (count($firstRowCells) < 2) {
            return false;
        }

        $headers = [];

        foreach ($firstRowCells as $cell) {
            $headers[] = strtolower($this->cleanText($this->extractHtmlFromElement($cell)));
        }

        $headerText = implode(' ', $headers);

        return str_contains($headerText, 'no')
            && (str_contains($headerText, 'kunci') || str_contains($headerText, 'answer') || str_contains($headerText, 'key'))
            && (str_contains($headerText, 'level') || str_contains($headerText, 'kesulitan') || str_contains($headerText, 'difficulty'));
    }

    private function parseAnswerKeyTable($table): void
    {
        foreach ($table->getRows() as $rowIndex => $row) {
            if ($rowIndex === 0) {
                continue;
            }

            $cells = $row->getCells();

            if (count($cells) < 2) {
                continue;
            }

            $numberText = $this->cleanText($this->extractHtmlFromElement($cells[0]));
            $answerText = $this->cleanText($this->extractHtmlFromElement($cells[1]));
            $difficultyText = isset($cells[3]) ? $this->cleanText($this->extractHtmlFromElement($cells[3])) : '';

            if (!preg_match('/\d+/', $numberText, $numberMatches)) {
                continue;
            }

            $number = (int) $numberMatches[0];

            if ($number <= 0 || $answerText === '') {
                continue;
            }

            $this->answerMap[$number] = [
                'answer' => $this->normalizeAnswer($answerText),
                'difficulty' => $this->normalizeDifficulty($difficultyText),
            ];
        }
    }

    private function isEnterpriseTable($table): bool
    {
        $rows = $table->getRows();

        if (count($rows) < 2) {
            return false;
        }

        $firstRowCells = $rows[0]->getCells();

        if (count($firstRowCells) >= 5) {
            $h1 = strtolower($this->cleanText($this->extractHtmlFromElement($firstRowCells[0] ?? null)));
            $h2 = strtolower($this->cleanText($this->extractHtmlFromElement($firstRowCells[1] ?? null)));

            return str_contains($h1, 'petunjuk') || str_contains($h2, 'soal');
        }

        return false;
    }

    private function convertEnterpriseTableToLines($table, array &$rawLines): void
    {
        foreach ($table->getRows() as $rowIndex => $row) {
            if ($rowIndex === 0) {
                continue;
            }

            $cells = $row->getCells();

            if (count($cells) < 9) {
                continue;
            }

            $reading = trim($this->extractHtmlFromElement($cells[0]));

            if (!empty(strip_tags($reading)) || $this->containsImage($reading)) {
                $rawLines[] = $reading;
            }

            $questionText = trim($this->extractHtmlFromElement($cells[1]));
            $rawLines[] = $rowIndex . '. ' . $questionText;

            for ($i = 0; $i < 5; $i++) {
                $option = trim($this->extractHtmlFromElement($cells[3 + $i]));

                if (!empty(strip_tags($option)) || $this->containsImage($option)) {
                    $letter = chr(65 + $i);
                    $rawLines[] = $letter . '. ' . $option;
                }
            }

            $answer = $this->cleanText($this->extractHtmlFromElement($cells[8]));

            if ($answer !== '') {
                $rawLines[] = 'Kunci: ' . $answer;
            }
        }
    }

    private function isBooleanGridTable($table): bool
    {
        $rows = $table->getRows();

        if (count($rows) < 2) {
            return false;
        }

        $firstRowCells = $rows[0]->getCells();

        if (count($firstRowCells) < 3) {
            return false;
        }

        $headers = [];

        foreach ($firstRowCells as $cell) {
            $headers[] = strtolower($this->cleanText($this->extractHtmlFromElement($cell)));
        }

        $headerText = implode(' ', $headers);

        $hasStatement = str_contains($headerText, 'pernyataan')
            || str_contains($headerText, 'statement')
            || str_contains($headerText, 'statements');

        $hasTrue = str_contains($headerText, 'benar')
            || str_contains($headerText, 'true');

        $hasFalse = str_contains($headerText, 'salah')
            || str_contains($headerText, 'false');

        return $hasStatement && $hasTrue && $hasFalse;
    }

    private function convertGridTableToLines($table, array &$rawLines): void
    {
        $letters = range('A', 'Z');
        $keys = [];
        $hasAnyMark = false;

        foreach ($table->getRows() as $rowIndex => $row) {
            if ($rowIndex === 0) {
                continue;
            }

            $cells = $row->getCells();

            if (count($cells) < 3) {
                continue;
            }

            /*
             * Support:
             * 1. Pernyataan | Benar | Salah
             * 2. No | Statements | True | False
             */
            if (count($cells) >= 4) {
                $statementCellIndex = 1;
                $trueCellIndex = 2;
                $falseCellIndex = 3;
            } else {
                $statementCellIndex = 0;
                $trueCellIndex = 1;
                $falseCellIndex = 2;
            }

            $statement = trim($this->extractHtmlFromElement($cells[$statementCellIndex]));

            if ($this->cleanText($statement) === '' && !$this->containsImage($statement)) {
                continue;
            }

            $letter = $letters[count($keys)] ?? $letters[$rowIndex - 1] ?? 'A';
            $rawLines[] = $letter . '. ' . $statement;

            $benarMark = $this->cleanText($this->extractHtmlFromElement($cells[$trueCellIndex]));
            $salahMark = $this->cleanText($this->extractHtmlFromElement($cells[$falseCellIndex]));

            if ($this->hasBooleanMark($benarMark) && !$this->hasBooleanMark($salahMark)) {
                $keys[] = 'B';
                $hasAnyMark = true;
            } elseif ($this->hasBooleanMark($salahMark) && !$this->hasBooleanMark($benarMark)) {
                $keys[] = 'S';
                $hasAnyMark = true;
            } else {
                $keys[] = null;
            }
        }

        if ($hasAnyMark && !empty($keys)) {
            $filteredKeys = array_filter($keys, fn($key) => $key !== null);

            if (count($filteredKeys) === count($keys)) {
                $rawLines[] = 'Kunci: ' . implode(',', $filteredKeys);
            }
        }
    }

    private function hasBooleanMark(?string $value): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return false;
        }

        return preg_match('/^(v|x|✓|✔|√|check|checked|ya|y|benar|salah|true|false|t|f|1)$/iu', $value) === 1;
    }

    private function extractHtmlFromElement($element): string
    {
        if ($element === null) {
            return '';
        }

        $html = '';

        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $html .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $child) {
                $html .= $this->extractHtmlFromElement($child);
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
            $html .= '<br>';
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Image) {
            $html .= $this->processImage($element);
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
            $html .= '[LIST_ITEM_MARKER] ';

            $textObject = $element->getTextObject();
            $html .= $textObject ? $this->extractHtmlFromElement($textObject) : $element->getText();

            $html .= '<br>';
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    $html .= $this->extractHtmlFromElement($cell) . ' ';
                }

                $html .= '<br>';
            }
        }

        if (method_exists($element, 'getElements') && !($element instanceof \PhpOffice\PhpWord\Element\TextRun)) {
            foreach ($element->getElements() as $child) {
                $html .= $this->extractHtmlFromElement($child);
            }
        }

        return $html;
    }

    private function processImage($imageElement): string
    {
        try {
            $source = $imageElement->getSource();
            $imageData = null;

            if (@is_readable($source) || str_starts_with($source, 'zip://') || str_starts_with($source, 'http')) {
                $imageData = file_get_contents($source);
            } else {
                $base64Image = $imageElement->getImageStringData(true);

                if (!empty($base64Image)) {
                    $imageData = base64_decode($base64Image);
                }
            }

            if (empty($imageData)) {
                return ' [Gambar Kosong] ';
            }

            $extension = $imageElement->getImageExtension() ?: 'png';
            if (in_array(strtolower($extension), ['emf', 'wmf', 'tiff', 'eps'])) {
                $extension = 'png';
            }
            $filename = 'img_' . time() . '_' . uniqid() . '.' . $extension;
            $path = 'uploads/questions/' . $filename;

            Storage::disk('public')->put($path, $imageData);

            $url = asset('storage/' . $path);

            return '<br><img src="' . $url . '" style="max-width: 100%; height: auto;" class="my-2"/><br>';
        } catch (\Throwable $e) {
            return ' [Gagal Gambar] ';
        }
    }

    private function sanitizeDocxImages($filePath): void
    {
        if (!class_exists('ZipArchive')) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            $unsupportedFiles = [];
            
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('/\.(emf|wmf|tiff|eps)$/i', $name)) {
                    $unsupportedFiles[] = $name;
                }
            }

            if (!empty($unsupportedFiles)) {
                // 1x1 transparent PNG base64
                $png1x1 = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
                
                foreach ($unsupportedFiles as $fileName) {
                    $zip->addFromString($fileName, $png1x1);
                }
            }
            
            $zip->close();
        }
    }
}