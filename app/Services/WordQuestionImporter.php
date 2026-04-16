<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionGroup;
use App\Models\ReadingText;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WordQuestionImporter
{
    protected $subjectId;
    protected $errors = [];

    public function __construct($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    /**
     * Main Import Logic: Full Table System
     */
    public function import($filePath)
    {
        $phpWord = IOFactory::load($filePath);
        $importedCount = 0;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                // Only process Tables
                if ($element instanceof Table) {
                    $rows = $element->getRows();
                    
                    foreach ($rows as $rowIndex => $row) {
                        // Skip header row (Usually contains 'No' or 'Soal')
                        if ($rowIndex === 0) continue;

                        $cells = $row->getCells();
                        
                        // We expect 13 columns for the Enterprise Template
                        if (count($cells) < 3) continue;

                        $data = $this->parseRow($cells);

                        if ($this->validate($data, $rowIndex)) {
                            try {
                                $this->save($data);
                                $importedCount++;
                            } catch (\Exception $e) {
                                $this->errors[] = "Baris " . ($rowIndex + 1) . ": Gagal simpan (" . $e->getMessage() . ")";
                            }
                        }
                    }
                }
            }
        }

        return [
            'count' => $importedCount,
            'errors' => $this->errors
        ];
    }

    /**
     * Map Cells to Data Array - Sync with 9-column Production Template
     * 0:Petunjuk, 1:Soal, 2:Jenis, 3:A, 4:B, 5:C, 6:D, 7:E, 8:JWB
     */
    private function parseRow($cells)
    {
        return [
            'reading'    => $this->getCellContent($cells[0] ?? null),
            'content'    => $this->getCellContent($cells[1] ?? null),
            'type'       => strtolower(trim($this->getCellContent($cells[2] ?? 'multiple_choice'))),
            'opsi_a'     => $this->getCellContent($cells[3] ?? null),
            'opsi_b'     => $this->getCellContent($cells[4] ?? null),
            'opsi_c'     => $this->getCellContent($cells[5] ?? null),
            'opsi_d'     => $this->getCellContent($cells[6] ?? null),
            'opsi_e'     => $this->getCellContent($cells[7] ?? null),
            'answer'     => strtoupper(trim($this->getCellContent($cells[8] ?? ''))),
            'difficulty' => 'easy', // Default because metadata column is hidden for simplicity
            'tags'       => '',
            'group'      => '',
        ];
    }

    /**
     * Extract Text and Images from a specific cell (Recursive Version)
     */
    private function getCellContent($cell)
    {
        // Defensive check
        if (!$cell || !is_object($cell)) {
            return is_string($cell) ? trim($cell) : '';
        }

        return trim($this->extractHtmlFromElement($cell));
    }

    /**
     * Super Scanner: Drills down into Word element layers
     */
    private function extractHtmlFromElement($element)
    {
        $html = '';

        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            // It's pure text
            $html .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
            // It's an enter/return
            $html .= '<br>';
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Image) {
            // It's an image
            $html .= $this->processImage($element);
        }

        // If it's a container (like Paragraph, TextRun, ListItem, Cell) -> Drill deeper
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $html .= $this->extractHtmlFromElement($child);
            }
        }

        return $html;
    }

    /**
     * Save Image Binary to Storage
     */
    private function processImage($imageElement)
    {
        try {
            $source = $imageElement->getSource();
            $imageData = null;

            if (@is_readable($source) || str_starts_with($source, 'zip://') || str_starts_with($source, 'http')) {
                $imageData = file_get_contents($source);
            } else {
                // Fallback decode base64 if source isn't an accessible file path
                $base64Image = $imageElement->getImageStringData(true); 
                if (!empty($base64Image)) $imageData = base64_decode($base64Image);
            }

            if (empty($imageData)) return ' [Gambar Kosong] ';

            $extension = $imageElement->getImageExtension() ?: 'png';
            $filename = 'img_' . time() . '_' . uniqid() . '.' . $extension;
            $path = 'uploads/questions/' . $filename;
            
            Storage::disk('public')->put($path, $imageData);
            
            $url = asset('storage/' . $path);
            return '<br><img src="' . $url . '" style="max-width: 100%; height: auto;" class="my-2"/><br>';
        } catch (\Exception $e) {
            return ' [Gagal Gambar] ';
        }
    }

    /**
     * Validation Logic
     */
    private function validate($data, $rowIndex)
    {
        $rowNum = $rowIndex + 1;

        if (empty($data['content'])) {
            $this->errors[] = "Baris $rowNum: Konten soal kosong.";
            return false;
        }

        if ($data['type'] === 'multiple_choice' || $data['type'] === 'pilihan_ganda') {
            if (empty($data['opsi_a']) || empty($data['opsi_b'])) {
                $this->errors[] = "Baris $rowNum: Pilihan Ganda butuh minimal Opsi A & B.";
                return false;
            }

            if (!in_array($data['answer'], ['A', 'B', 'C', 'D', 'E'])) {
                $this->errors[] = "Baris $rowNum: Kunci Jawaban '{$data['answer']}' tidak valid (Gunakan A/B/C/D/E).";
                return false;
            }
        }

        return true;
    }

    /**
     * Save to Database
     */
    private function save($data)
    {
        DB::transaction(function () use ($data) {
            // Auto-Generate ReadingText if present
            $readingTextId = null;
            if (!empty(trim(strip_tags($data['reading']))) || str_contains($data['reading'], '<img')) {
                $rt = \App\Models\ReadingText::create([
                    'subject_id' => $this->subjectId,
                    'code' => 'PET-' . time() . '-' . uniqid(),
                    'title' => 'Petunjuk Soal Otomatis',
                    'content' => $data['reading']
                ]);
                $readingTextId = $rt->id;
            }

            $questionGroupId = null;
            if ($data['group']) {
                $grp = QuestionGroup::firstOrCreate([
                    'name' => $data['group'],
                    'subject_id' => $this->subjectId
                ]);
                $questionGroupId = $grp->id;
            }

            $difficulty = in_array($data['difficulty'], ['easy', 'medium', 'hard']) ? $data['difficulty'] : 'easy';

            // Create Question
            $question = Question::create([
                'subject_id' => $this->subjectId,
                'reading_text_id' => $readingTextId,
                'question_group_id' => $questionGroupId,
                'content' => $data['content'],
                'type' => ($data['type'] === 'essay' || $data['type'] === 'uraian') ? 'essay' : 'multiple_choice',
                'difficulty' => $difficulty,
                'created_by' => auth()->id(),
            ]);

            // Handle Tags
            if ($data['tags']) {
                $tagNames = array_map('trim', explode(',', $data['tags']));
                foreach ($tagNames as $name) {
                    $tag = Tag::firstOrCreate(['name' => $name]);
                    $question->tags()->attach($tag->id);
                }
            }

            // Handle Options
            if ($question->type === 'multiple_choice') {
                $optionsMap = [
                    'A' => $data['opsi_a'], 'B' => $data['opsi_b'], 'C' => $data['opsi_c'], 
                    'D' => $data['opsi_d'], 'E' => $data['opsi_e']
                ];
                foreach ($optionsMap as $key => $content) {
                    if (!empty($content)) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'content' => $content,
                            'is_correct' => $key == $data['answer']
                        ]);
                    }
                }
            }
        });
    }
}
