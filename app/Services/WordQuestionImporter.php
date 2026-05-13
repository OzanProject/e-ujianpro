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

    /**
     * Import utama.
     *
     * Prinsip parser:
     * 1. Apa pun sebelum nomor soal = stimulus/bacaan soal berikutnya.
     * 2. Baris bernomor 1. / 2. / dst = pertanyaan inti/stem.
     * 3. A. / B. / C. / D. / E. = opsi.
     * 4. Teks/gambar setelah opsi selesai = stimulus soal berikutnya.
     * 5. Kunci boleh ada di tabel akhir No | Kunci | Pengecoh | Level.
     */
    public function import($filePath): array
    {
        $this->errors = [];
        $this->answerMap = [];

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
            $line = trim($line);
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

            if (!$hasImage && preg_match('/^(\d+)\.\s+(.*)/u', $cleanLine, $questionMatches)) {
                if ($currentQuestion) {
                    $questions[] = $currentQuestion;
                }

                $number = (int) $questionMatches[1];
                $content = preg_replace('/^(\s*<[^>]*>\s*)*\d+\.(\s*<[^>]*>\s*)*\s*/iu', '', $line);

                $answerData = $this->answerMap[$number] ?? [];
                $answer = $answerData['answer'] ?? '';

                $currentQuestion = [
                    'number' => $number,
                    'reading' => $pendingReading,
                    'content' => $content,
                    'options' => [],
                    'answer' => $answer,
                    'difficulty' => $answerData['difficulty'] ?? 'easy',
                    'type' => $this->detectQuestionType($answer),
                ];

                $pendingReading = '';
                continue;
            }

            if ($currentQuestion && !$hasImage && preg_match('/^(?:\[\s*\]\s*)?([A-Ea-e])[\.)]\s*(.*)/iu', $cleanLine, $optionMatches)) {
                $letter = strtoupper($optionMatches[1]);
                $optionIndex = $this->letterToIndex($letter);
                $optionContent = preg_replace('/^(\s*<[^>]*>\s*)*(?:\[\s*\]\s*)?[A-Ea-e][\.)](\s*<[^>]*>\s*)*\s*/iu', '', $line);

                $currentQuestion['options'][$optionIndex] = [
                    'content' => $optionContent,
                ];

                continue;
            }

            if ($currentQuestion && !$hasImage && preg_match('/^(?:Kunci|Jawaban):\s*(.*)/iu', $cleanLine, $answerMatches)) {
                $answer = $this->normalizeAnswer($answerMatches[1]);
                $currentQuestion['answer'] = $answer;
                $currentQuestion['type'] = $this->detectQuestionType($answer);
                continue;
            }

            if ($currentQuestion && !$hasImage && preg_match('/^(?:Tingkat|Kesulitan|Level):\s*(.*)/iu', $cleanLine, $difficultyMatches)) {
                $currentQuestion['difficulty'] = $this->normalizeDifficulty($difficultyMatches[1]);
                continue;
            }

            if (!$currentQuestion) {
                $pendingReading = $this->appendHtml($pendingReading, $line);
                continue;
            }

            if (empty($currentQuestion['options'])) {
                if ($hasImage) {
                    $currentQuestion['reading'] = $this->appendHtml($currentQuestion['reading'], $line);
                } else {
                    $currentQuestion['content'] = $this->appendHtml($currentQuestion['content'], $line);
                }
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

        $parts = explode('<br>', $html);

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part !== '' || $this->containsImage($part)) {
                $rawLines[] = $part;
            }
        }
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
        if ($hasImage) {
            return true;
        }

        if ($cleanLine === '') {
            return false;
        }

        $upper = strtoupper(trim($cleanLine));

        foreach (['KUNCI:', 'JAWABAN:', 'TINGKAT:', 'KESULITAN:', 'LEVEL:'] as $prefix) {
            if (str_starts_with($upper, $prefix)) {
                return false;
            }
        }

        if (preg_match('/^(?:\[\s*\]\s*)?[A-Ea-e][\.)]\s+/u', $cleanLine)) {
            return false;
        }

        if ($nextCleanLine && $this->isNumberedQuestionLine($nextCleanLine)) {
            return true;
        }

        $words = preg_split('/\s+/u', trim($cleanLine));

        return count(array_filter($words)) >= 5;
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

    private function isNumberedQuestionLine(string $line): bool
    {
        return preg_match('/^\d+\.\s+/u', trim($line)) === 1;
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

        if (preg_match_all('/\d+\s*[\.]\s*([BS])/iu', $answer, $matches) || preg_match_all('/\d+\s*[\)]\s*([BS])/iu', $answer, $matches)) {
            return implode(',', array_map('strtoupper', $matches[1]));
        }

        $compact = preg_replace('/\s+/u', '', $answer);

        if (preg_match('/^[BS](,[BS])+$/iu', $compact)) {
            return strtoupper($compact);
        }

        if (preg_match_all('/[A-E]/iu', $compact, $matches)) {
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
                if (!in_array($part, ['B', 'S'], true)) {
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
            if ($part === 'B') {
                $values[$index] = 1;
            } elseif ($part === 'S') {
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
            && str_contains($headerText, 'kunci')
            && (str_contains($headerText, 'level') || str_contains($headerText, 'kesulitan'));
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

        $h1 = strtolower($this->cleanText($this->extractHtmlFromElement($firstRowCells[0])));
        $h2 = strtolower($this->cleanText($this->extractHtmlFromElement($firstRowCells[1])));
        $h3 = strtolower($this->cleanText($this->extractHtmlFromElement($firstRowCells[2])));

        return str_contains($h1, 'pernyataan')
            && str_contains($h2, 'benar')
            && str_contains($h3, 'salah');
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

            $statement = trim($this->extractHtmlFromElement($cells[0]));

            if ($this->cleanText($statement) === '' && !$this->containsImage($statement)) {
                continue;
            }

            $letter = $letters[$rowIndex - 1] ?? 'A';
            $rawLines[] = $letter . '. ' . $statement;

            $benarMark = $this->cleanText($this->extractHtmlFromElement($cells[1]));
            $salahMark = $this->cleanText($this->extractHtmlFromElement($cells[2]));

            if ($this->hasBooleanMark($benarMark) && !$this->hasBooleanMark($salahMark)) {
                $keys[] = 'B';
                $hasAnyMark = true;
            } elseif ($this->hasBooleanMark($salahMark) && !$this->hasBooleanMark($benarMark)) {
                $keys[] = 'S';
                $hasAnyMark = true;
            }
        }

        if ($hasAnyMark && !empty($keys)) {
            $rawLines[] = 'Kunci: ' . implode(',', $keys);
        }
    }

    private function hasBooleanMark(?string $value): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return false;
        }

        return preg_match('/^(v|x|✓|✔|√|check|checked|ya|y|benar|salah|1)$/iu', $value) === 1;
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
            $filename = 'img_' . time() . '_' . uniqid() . '.' . $extension;
            $path = 'uploads/questions/' . $filename;

            Storage::disk('public')->put($path, $imageData);

            $url = asset('storage/' . $path);

            return '<br><img src="' . $url . '" style="max-width: 100%; height: auto;" class="my-2"/><br>';
        } catch (\Throwable $e) {
            return ' [Gagal Gambar] ';
        }
    }
}
