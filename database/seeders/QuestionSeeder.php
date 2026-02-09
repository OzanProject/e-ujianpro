<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuestionOption;

class QuestionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // 1. Get or Create Subject
    $subject = Subject::first();
    if (!$subject) {
      $subject = Subject::create([
        'code' => 'UMUM-01',
        'name' => 'Pengetahuan Umum',
      ]);
      $this->command->info('Created Subject: Pengetahuan Umum');
    } else {
      $this->command->info('Using Subject: ' . $subject->name);
    }

    // 2. Sample Data for Multiple Choice (PG)
    $pgQuestions = [
      [
        'content' => 'Apa nama planet terdekat dari Matahari?',
        'options' => [
          ['content' => 'Venus', 'is_correct' => false],
          ['content' => 'Merkurius', 'is_correct' => true],
          ['content' => 'Bumi', 'is_correct' => false],
          ['content' => 'Mars', 'is_correct' => false],
          ['content' => 'Jupiter', 'is_correct' => false],
        ]
      ],
      [
        'content' => 'Benua terbesar di dunia adalah?',
        'options' => [
          ['content' => 'Afrika', 'is_correct' => false],
          ['content' => 'Eropa', 'is_correct' => false],
          ['content' => 'Amerika', 'is_correct' => false],
          ['content' => 'Asia', 'is_correct' => true],
          ['content' => 'Australia', 'is_correct' => false],
        ]
      ],
      [
        'content' => 'Simbol kimia untuk emas adalah?',
        'options' => [
          ['content' => 'Ag', 'is_correct' => false],
          ['content' => 'Au', 'is_correct' => true],
          ['content' => 'Fe', 'is_correct' => false],
          ['content' => 'Pb', 'is_correct' => false],
          ['content' => 'Zn', 'is_correct' => false],
        ]
      ],
      [
        'content' => 'Hewan apa yang disebut sebagai raja hutan?',
        'options' => [
          ['content' => 'Gajah', 'is_correct' => false],
          ['content' => 'Harimau', 'is_correct' => false],
          ['content' => 'Singa', 'is_correct' => true],
          ['content' => 'Macan Tutul', 'is_correct' => false],
          ['content' => 'Serigala', 'is_correct' => false],
        ]
      ],
      [
        'content' => 'Berapa hasil dari 8 x 7?',
        'options' => [
          ['content' => '54', 'is_correct' => false],
          ['content' => '56', 'is_correct' => true],
          ['content' => '64', 'is_correct' => false],
          ['content' => '48', 'is_correct' => false],
          ['content' => '49', 'is_correct' => false],
        ]
      ]
    ];

    // 3. Sample Data for Essay
    $essayQuestions = [
      'Jelaskan proses terjadinya fotosintesis pada tumbuhan!',
      'Sebutkan 5 sila dalam Pancasila!',
      'Apa perbedaan antara sel hewan dan sel tumbuhan?',
      'Ceritakan secara singkat sejarah kemerdekaan Indonesia!',
      'Bagaimana cara menjaga kesehatan mata?'
    ];

    // 4. Insert PG Questions
    foreach ($pgQuestions as $data) {
      $question = Question::create([
        'subject_id' => $subject->id,
        'type' => 'multiple_choice',
        'content' => $data['content']
      ]);

      foreach ($data['options'] as $optionData) {
        QuestionOption::create([
          'question_id' => $question->id,
          'content' => $optionData['content'],
          'is_correct' => $optionData['is_correct']
        ]);
      }
    }
    $this->command->info('Seeded 5 Multiple Choice questions.');

    // 5. Insert Essay Questions
    foreach ($essayQuestions as $content) {
      Question::create([
        'subject_id' => $subject->id,
        'type' => 'essay',
        'content' => $content
      ]);
    }
    $this->command->info('Seeded 5 Essay questions.');
  }
}
