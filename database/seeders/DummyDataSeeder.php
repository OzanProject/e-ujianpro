<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Institution;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ExamPackage;
use App\Models\ExamSession;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // 0. Ensure Host / Admin Lembaga
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@smadummy.com'],
            [
                'name' => 'Admin SMA Dummy',
                'password' => Hash::make('password'),
                'role' => 'admin_lembaga',
                'status' => 'active',
            ]
        );

        // 1. Ensure Institution
        $institution = Institution::first();
        if (!$institution) {
            $institution = Institution::create([
                'user_id' => $admin->id,
                'name' => 'SMA Dummy Sejahtera',
                'email' => 'admin@smadummy.com',
                'phone' => '08123456789',
                'address' => 'Jl. Dummy No. 1',
                'city' => 'Jakarta Selatan',
                'head_master' => 'Dr. Budi Santoso, M.Pd.',
                'nip_head_master' => '19800101 200501 1 001',
            ]);
        } else {
            // Update existing for dynamic data check
            $institution->update([
                'user_id' => $admin->id,
                'city' => 'Jakarta Selatan',
                'head_master' => 'Dr. Budi Santoso, M.Pd.',
                'nip_head_master' => '19800101 200501 1 001',
            ]);
        }

        // 2. Create Subjects
        $subjectsMap = [
            'Matematika' => 'MAT',
            'Bahasa Indonesia' => 'BIN',
            'Bahasa Inggris' => 'BIG',
            'IPA' => 'IPA',
            'IPS' => 'IPS',
            'PKN' => 'PKN',
            'Sejarah' => 'SEJ',
            'Fisika' => 'FIS',
            'Kimia' => 'KIM',
            'Biologi' => 'BIO'
        ];
        $subjects = [];
        foreach ($subjectsMap as $subName => $code) {
            $subjects[] = Subject::firstOrCreate(
                ['name' => $subName],
                ['code' => $code]
            );
        }

        // 2b. Create Teacher
        $this->command->info('Creating Dummy Teacher...');
        $teacher = \App\Models\User::firstOrCreate(
            ['email' => 'guru@smadummy.com'],
            [
                'name' => 'Guru Teladan',
                'password' => Hash::make('password'),
                'role' => 'pengajar',
                'status' => 'active',
                'created_by' => $admin->id,
            ]
        );
        // Attach all created subjects to this teacher
        $teacher->subjects()->sync(collect($subjects)->pluck('id'));

        // 3. Create Student Groups
        $groups = [];
        $groupNames = ['X IPA 1', 'X IPS 1', 'XI IPA 1', 'XI IPS 1', 'XII IPA 1', 'XII IPS 1'];
        foreach ($groupNames as $gName) {
            $groups[] = StudentGroup::firstOrCreate(['name' => $gName]);
        }

        // 4. Create Students (50 Data)
        $this->command->info('Creating 50 Dummy Students...');
        for ($i = 0; $i < 50; $i++) {
            $group = $faker->randomElement($groups);
            
            // Generate NIS unique
            $nis = $faker->unique()->numberBetween(10000, 99999);
            
            Student::create([
                'student_group_id' => $group->id,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'nis' => $nis,
                'password' => Hash::make('123456'),
                'kelas' => $group->name,
                'jurusan' => str_contains($group->name, 'IPA') ? 'IPA' : 'IPS',
            ]);
        }

        // 5. Create Questions for each Subject
        $this->command->info('Creating Questions for each Subject...');
        foreach ($subjects as $subject) {
            // Create 20 questions per subject
            for ($q = 1; $q <= 20; $q++) {
                $question = Question::create([
                    'subject_id' => $subject->id,
                    'type' => 'multiple_choice',
                    'content' => "<p>Pertanyaan nomor $q untuk mata pelajaran {$subject->name}. Apa jawaban yang benar?</p>",
                ]);

                // Options
                $correctIndex = rand(0, 4); 
                $options = ['A', 'B', 'C', 'D', 'E'];
                foreach ($options as $idx => $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'content' => "<p>Pilihan Jawaban $opt</p>",
                        'is_correct' => ($idx == $correctIndex),
                    ]);
                }
            }
        }

        // 6. Create Exam Package
        $this->command->info('Creating Exam Package...');
        $pkg = ExamPackage::create([
            'subject_id' => $subjects[0]->id,
            'name' => 'Paket Ujian Akhir Semester (Demo)',
        ]);

        // Attach questions from first 3 subjects to package
        $limitedSubjects = array_slice($subjects, 0, 1); // Limit to just the matching subject to be safe
        foreach ($limitedSubjects as $subj) {
            $qs = Question::where('subject_id', $subj->id)->limit(5)->get();
            foreach ($qs as $q) {
                 DB::table('exam_package_question')->insert([
                     'exam_package_id' => $pkg->id,
                     'question_id' => $q->id,
                     'created_at' => now(),
                     'updated_at' => now(),
                 ]);
            }
        }

        // 7. Create Exam Session
        $this->command->info('Creating Exam Session...');
        ExamSession::create([
             'subject_id' => $subjects[0]->id,
             'exam_package_id' => $pkg->id,
             'title' => 'Ujian Akhir Semester Ganjil',
             'start_time' => now()->subDay(),
             'end_time' => now()->addDays(7),
             'duration' => 90,
             'description' => 'Ujian ini aktif selama 1 minggu.',
             'is_active' => true, 
        ]);
        
        $this->command->info('Dummy Data Generation Completed!');
    }
}
