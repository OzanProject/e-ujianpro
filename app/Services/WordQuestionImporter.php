<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionGroup;
use App\Models\ReadingText;
use Illuminate\Support\Facades\DB;

class WordQuestionImporter
{
    protected $subjectId;

    public function __construct($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    public function import($filePath)
    {
        $phpWord = IOFactory::load($filePath);
        $importedCount = 0;
        $errors = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                // Check if element is a table
                if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    $this->processTable($element, $importedCount, $errors);
                }
            }
        }

        return [
            'count' => $importedCount,
            'errors' => $errors
        ];
    }

    private function processTable($table, &$importedCount, &$errors)
    {
        $rows = $table->getRows();
        
        // Skip header row if it contains known headers
        $startIndex = 0;
        if (count($rows) > 0) {
            $firstCell = $this->getCellText($rows[0]->getCells()[0]);
            if (stripos($firstCell, 'soal') !== false || stripos($firstCell, 'no') !== false) {
                $startIndex = 1;
            }
        }

        for ($i = $startIndex; $i < count($rows); $i++) {
            try {
                DB::beginTransaction();

                $cells = $rows[$i]->getCells();
                
                // Expected columns: 
                // 0: Soal, 1: Jenis, 2-6: Opsi A-E, 7: Jawaban, 8: Kode Bacaan, 9: Grup Soal
                // Adjust index if there is a Numbering column at start
                
                $colOffset = 0;
                // Map cell contents
                $texts = [];
                foreach ($cells as $cell) {
                    $texts[] = $this->getCellText($cell);
                }

                // Validasi minimal kolom
                if (count($texts) < 5) {
                    DB::rollBack();
                    continue; 
                }

                // Simple heuristic: If first column is short number, shift offset
                if (is_numeric(trim($texts[0])) && strlen(trim($texts[0])) < 4) {
                     $colOffset = 1;
                }

                $soal = $texts[$colOffset] ?? '';
                if (empty($soal)) {
                    DB::rollBack();
                    continue;
                }

                $jenis = strtolower(trim($texts[$colOffset + 1] ?? 'multiple_choice'));
                if ($jenis == 'essay' || $jenis == 'uraian') $jenis = 'essay';
                else $jenis = 'multiple_choice';

                $opsiA = $texts[$colOffset + 2] ?? '';
                $opsiB = $texts[$colOffset + 3] ?? '';
                $opsiC = $texts[$colOffset + 4] ?? '';
                $opsiD = $texts[$colOffset + 5] ?? '';
                $opsiE = $texts[$colOffset + 6] ?? '';
                
                $jawaban = strtoupper(trim($texts[$colOffset + 7] ?? ''));
                $kodeBacaan = trim($texts[$colOffset + 8] ?? '');
                $grupSoal = trim($texts[$colOffset + 9] ?? '');

                // Process Relations
                $readingTextId = null;
                if ($kodeBacaan) {
                    $rt = ReadingText::where('code', $kodeBacaan)->first();
                    if ($rt) $readingTextId = $rt->id;
                }

                $questionGroupId = null;
                if ($grupSoal) {
                    $grp = QuestionGroup::firstOrCreate([
                        'name' => $grupSoal,
                        'subject_id' => $this->subjectId
                    ]);
                    $questionGroupId = $grp->id;
                }

                // Create Question
                $question = Question::create([
                    'subject_id' => $this->subjectId,
                    'content' => $soal,
                    'type' => $jenis,
                    'reading_text_id' => $readingTextId,
                    'question_group_id' => $questionGroupId
                ]);

                if ($jenis == 'multiple_choice') {
                    $options = [
                        'A' => $opsiA, 'B' => $opsiB, 'C' => $opsiC, 'D' => $opsiD, 'E' => $opsiE
                    ];
                    foreach ($options as $key => $content) {
                        if ($content) {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'content' => $content,
                                'is_correct' => ($key == $jawaban)
                            ]);
                        }
                    }
                }
                
                DB::commit();
                $importedCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }
    }

    private function getCellText($cell)
    {
        // Recursively extract text from cell elements (TextRun, Text)
        $text = '';
        foreach ($cell->getElements() as $element) {
            if (method_exists($element, 'getText')) {
                $text .= $element->getText();
            } elseif (method_exists($element, 'getElements')) {
                foreach ($element->getElements() as $child) {
                     if (method_exists($child, 'getText')) {
                        $text .= $child->getText();
                     }
                }
            }
        }
        return trim($text);
    }
}
