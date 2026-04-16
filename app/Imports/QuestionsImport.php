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

    public function headingRow(): int
    {
        return 7; // Header starts at row 7 in the new professional template
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

                    // Metadata: Difficulty
                    $difficulty = 'easy';
                    if (isset($row['tingkat_kesulitan'])) {
                        $diffInput = strtolower(trim($row['tingkat_kesulitan']));
                        if (in_array($diffInput, ['easy', 'medium', 'hard'])) {
                            $difficulty = $diffInput;
                        }
                    }

                    // Auto Create Reading Text (Petunjuk/Bacaan)
                    $readingTextId = null;
                    if (isset($row['petunjuk_bacaan']) && !empty(trim($row['petunjuk_bacaan']))) {
                        $rt = \App\Models\ReadingText::create([
                            'subject_id' => $this->subjectId,
                            'title' => 'Petunjuk Teks Ekstraksi Excel',
                            'code' => 'PET-XLS-' . time() . '-' . uniqid(),
                            'content' => nl2br(trim($row['petunjuk_bacaan']))
                        ]);
                        $readingTextId = $rt->id;
                    }

                    // Grup Soal diabaikan sementara untuk simplifikasi template
                    $questionGroupId = null;

                    // Create Question
                    $question = Question::create([
                        'subject_id' => $this->subjectId,
                        'reading_text_id' => $readingTextId,
                        'question_group_id' => $questionGroupId,
                        'content' => $row['soal'],
                        'type' => $type,
                        'difficulty' => $difficulty,
                        'created_by' => auth()->id(),
                    ]);

                    // Handle Tags
                    if (isset($row['tags']) && !empty($row['tags'])) {
                        $tagNames = array_map('trim', explode(',', $row['tags']));
                        $tagIds = [];
                        foreach ($tagNames as $name) {
                            $tag = \App\Models\Tag::firstOrCreate(['name' => $name]);
                            $tagIds[] = $tag->id;
                        }
                        $question->tags()->sync($tagIds);
                    }

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
