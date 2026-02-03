<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Subject;
use App\Models\ExamPackage;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ExamSession;
use App\Models\Student;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Students
        $student1 = Student::create([
            'user_id' => null, // Decoupled
            'name' => 'Budi Santoso',
            'nis' => '123456',
            'password' => Hash::make('123456'), // Password matches NIS for simplicity
            'kelas' => 'XII IPA 1',
            'jurusan' => 'IPA'
        ]);
        
        $student2 = Student::create([
            'user_id' => null,
            'name' => 'Siti Aminah',
            'nis' => '654321',
            'password' => Hash::make('654321'),
            'kelas' => 'XII IPA 1',
            'jurusan' => 'IPA'
        ]);

        $this->command->info('Students created: Budi (123456) & Siti (654321)');

        // 2. Create Subject
        $subject = Subject::create([
            'code' => 'MTK-XII',
            'name' => 'Matematika XII',
        ]);

        // 3. Create Exam Package
        $package = ExamPackage::create([
            'subject_id' => $subject->id,
            'name' => 'Paket A - Ujian Akhir Semester',
            'code' => 'UAS-MTK-A'
        ]);

        // 4. Create Questions (5 Questions)
        $questionsData = [
            [
                'content' => 'Berapakah hasil dari 5 + 5 x 2?',
                'options' => [
                    ['content' => '30', 'is_correct' => false],
                    ['content' => '20', 'is_correct' => false],
                    ['content' => '15', 'is_correct' => true],
                    ['content' => '10', 'is_correct' => false],
                    ['content' => '25', 'is_correct' => false],
                ]
            ],
            [
                'content' => 'Jika x = 2, berapakah nilai 2x + 10?',
                'options' => [
                    ['content' => '12', 'is_correct' => false],
                    ['content' => '14', 'is_correct' => true],
                    ['content' => '20', 'is_correct' => false],
                    ['content' => '4', 'is_correct' => false],
                    ['content' => '10', 'is_correct' => false],
                ]
            ],
            [
                'content' => 'Ibu kota Indonesia saat ini (2024) adalah?',
                'options' => [
                    ['content' => 'Jakarta', 'is_correct' => true],
                    ['content' => 'Surabaya', 'is_correct' => false],
                    ['content' => 'Medan', 'is_correct' => false],
                    ['content' => 'Bandung', 'is_correct' => false],
                    ['content' => 'IKN', 'is_correct' => false], // tricky
                ]
            ],
            [
                'content' => 'Siapakah penemu bola lampu?',
                'options' => [
                    ['content' => 'Einstein', 'is_correct' => false],
                    ['content' => 'Thomas Edison', 'is_correct' => true],
                    ['content' => 'Tesla', 'is_correct' => false],
                    ['content' => 'Newton', 'is_correct' => false],
                    ['content' => 'Graham Bell', 'is_correct' => false],
                ]
            ],
            [
                'content' => 'Berapa jumlah kaki laba-laba?',
                'options' => [
                    ['content' => '4', 'is_correct' => false],
                    ['content' => '6', 'is_correct' => false],
                    ['content' => '8', 'is_correct' => true],
                    ['content' => '10', 'is_correct' => false],
                    ['content' => '12', 'is_correct' => false],
                ]
            ]
        ];

        foreach ($questionsData as $qData) {
            $question = Question::create([
                'subject_id' => $subject->id,
                'type' => 'multiple_choice',
                'content' => $qData['content']
            ]);

            // Link to Package
            // Note: We need to populate exam_package_question pivot
            // Assuming DB relationship is setup or we do it manually via DB query if relation method missing
            // But we have $package->questions() belongsToMany
            $package->questions()->attach($question->id);

            // Create Options
            foreach ($qData['options'] as $optData) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'content' => $optData['content'],
                    'is_correct' => $optData['is_correct']
                ]);
            }
        }

        // 5. Create Active Exam Session
        ExamSession::create([
            'title' => 'Ujian Matematika XII',
            'subject_id' => $subject->id,
            'exam_package_id' => $package->id,
            'start_time' => now()->subHours(1), // Started 1 hour ago
            'end_time' => now()->addHours(5),   // Ends in 5 hours
            'duration' => 90, // 90 minutes
            'description' => 'Ujian Akhir Semester Matematika. Kerjakan dengan jujur.',
            'is_active' => true
        ]);

        // 6. Create Finished Exam Session (For History Test)
        $finishedSession = ExamSession::create([
            'title' => 'Ujian Percobaan (Selesai)',
            'subject_id' => $subject->id,
            'exam_package_id' => $package->id,
            'start_time' => now()->subDays(2),
            'end_time' => now()->subDays(1),
            'duration' => 60,
            'description' => 'Ujian percobaan yang sudah lewat.',
            'is_active' => false
        ]);

        $this->command->info('Exam Data seeded: 1 Subject, 1 Package (5 Qs), 1 Active Session, 1 Finished Session.');
    }
}
