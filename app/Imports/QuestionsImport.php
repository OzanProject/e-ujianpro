<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToCollection, WithHeadingRow
{
    protected $subjectId;

    public function __construct($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Validate required fields
                 if (!isset($row['soal']) || empty($row['soal'])) {
                    continue;
                }

                \Illuminate\Support\Facades\DB::transaction(function () use ($row) {
                    // Determine Type
                    $type = 'multiple_choice'; 
                    if (isset($row['jenis'])) {
                        $inputType = strtolower(trim($row['jenis']));
                        if (in_array($inputType, ['essay', 'uraian'])) {
                            $type = 'essay';
                        }
                    }

                    // Find Reading Text by Code
                    $readingTextId = null;
                    if (isset($row['kode_bacaan']) && !empty($row['kode_bacaan'])) {
                         $rt = \App\Models\ReadingText::where('code', trim($row['kode_bacaan']))->first();
                         if ($rt) {
                             $readingTextId = $rt->id;
                         }
                    }

                    // Create Question
                    $question = Question::create([
                        'subject_id' => $this->subjectId,
                        'reading_text_id' => $readingTextId,
                        'content' => $row['soal'],
                        'type' => $type,
                    ]);

                    // Create Options if Multiple Choice
                    if ($type === 'multiple_choice') {
                        $optionsData = [
                            'A' => $row['opsi_a'] ?? null,
                            'B' => $row['opsi_b'] ?? null,
                            'C' => $row['opsi_c'] ?? null,
                            'D' => $row['opsi_d'] ?? null,
                            'E' => $row['opsi_e'] ?? null,
                        ];

                        $correctAnswer = isset($row['jawaban']) ? strtoupper(trim($row['jawaban'])) : '';

                        foreach ($optionsData as $key => $content) {
                            if (!empty($content)) {
                                QuestionOption::create([
                                    'question_id' => $question->id,
                                    'content' => $content,
                                    'is_correct' => ($key === $correctAnswer),
                                ]);
                            }
                        }
                    }
                });
            } catch (\Exception $e) {
                // Log error or count failure? 
                // For now, silent skip prevents crash, but logging is better.
                // Assuming simple user experience: Skip bad rows.
                continue; 
            }
        }
    }
}
