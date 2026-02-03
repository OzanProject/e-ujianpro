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
use App\Models\ExamType;
use App\Models\ReadingText;
use App\Models\User;

class OneSchoolSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        $this->command->info('1. Membuat Akun Admin Sekolah...');
        
        // 1. Admin Sekolah
        $admin = User::firstOrCreate(
            ['email' => 'sekolah@demo.com'],
            [
                'name' => 'Admin Sekolah Demo',
                'password' => Hash::make('12345678'),
                'role' => 'admin_lembaga',
                'status' => 'active',
                'max_students' => 100,
                'points_balance' => 99999,
            ]
        );

        // 2. Data Institusi
        $institution = Institution::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'name' => 'SMA Demo Indonesia',
                'subdomain' => 'demo',
                'email' => 'sekolah@demo.com',
                'phone' => '08129999999',
                'address' => 'Jl. Pendidikan No. 1, Jakarta',
                'city' => 'Jakarta Selatan',
                'type' => 'SMA/Sederajat',
                'head_master' => 'Budi Santoso, M.Pd',
                'nip_head_master' => '198001012010011005',
            ]
        );

        $this->command->info('2. Membuat Mata Pelajaran...');
        
        // 3. Mata Pelajaran
        $subjects = [];
        $subjectNames = ['Bahasa Indonesia', 'Bahasa Inggris', 'Matematika'];
        foreach ($subjectNames as $name) {
            $code = strtoupper(substr($name, 0, 3));
            // Use updateOrCreate to avoid unique constraint violation if exists
            $subjects[$name] = Subject::updateOrCreate(
                ['name' => $name, 'created_by' => $admin->id],
                ['code' => $code . '-' . rand(100,999)] // Randomize code slightly to avoid collision if needed, or rely on name
            );
        }

        $this->command->info('3. Membuat Akun Guru & Siswa...');

        // 4. Guru
        $teacher = User::firstOrCreate(
            ['email' => 'guru@demo.com'],
            [
                'name' => 'Guru Demo (B. Indo)',
                'password' => Hash::make('12345678'),
                'role' => 'pengajar',
                'status' => 'active',
                'created_by' => $admin->id,
            ]
        );
        $teacher->subjects()->sync([$subjects['Bahasa Indonesia']->id]);

        // 5. Kelas & Siswa
        $group = StudentGroup::firstOrCreate(
            ['name' => 'X IPA 1', 'created_by' => $admin->id] // Removed institution_id as it doesn't exist
        );

        // Buat 5 Siswa
        for ($i = 1; $i <= 5; $i++) {
            Student::firstOrCreate(
                ['nis' => "100$i"],
                [
                    'name' => "Siswa Demo $i",
                    'email' => "siswa$@demo.com",
                    'password' => Hash::make('12345678'),
                    'student_group_id' => $group->id,
                    'kelas' => $group->name,
                    'jurusan' => 'IPA',
                    'created_by' => $admin->id
                ]
            );
        }

        $this->command->info('4. Membuat Data Ujian (Bacaan, Soal, Paket)...');

        // 6. Reading Text (Wacana)
        $readingText = ReadingText::create([
            'subject_id' => $subjects['Bahasa Indonesia']->id,
            'title' => 'Kisah Sang Kancil',
            'content' => '<p>Pada suatu hari, Kancil sedang berjalan-jalan di pinggir hutan...</p><p>(Ini adalah contoh teks bacaan panjang yang akan muncul di atas soal)</p>',
            'code' => 'RT-001'
        ]);

        // 7. Bank Soal (5 Soal)
        // Soal 1-2 pakai bacaan
        $pkg = ExamPackage::create([
            'subject_id' => $subjects['Bahasa Indonesia']->id,
            'name' => 'Paket Ujian B. Indo (Demo)',
            'created_by' => $admin->id,
            'code' => 'PKT-INDO-01'
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $pakaibacaan = ($i <= 2) ? $readingText->id : null;
            
            $q = Question::create([
                'subject_id' => $subjects['Bahasa Indonesia']->id,
                'reading_text_id' => $pakaibacaan,
                'content' => "<p>Pertanyaan Nomor $i. Manakah jawaban yang paling tepat?</p>",
                'type' => 'multiple_choice',
            ]);

            // Options
            foreach (['A', 'B', 'C', 'D', 'E'] as $idx => $opt) {
                QuestionOption::create([
                    'question_id' => $q->id,
                    'content' => "Pilihan Jawaban $opt",
                    'is_correct' => ($idx == 0) // Kunci A
                ]);
            }

            // Bind to Package
            $pkg->questions()->attach($q->id);
        }

        // 8. Jenis Ujian
        $type = ExamType::firstOrCreate(
            ['name' => 'Penilaian Harian', 'created_by' => $admin->id],
            ['description' => 'Ujian harian biasa', 'is_active' => true]
        );

        $this->command->info('5. Menjadwalkan Sesi Ujian...');

        // 9. Sesi Ujian
        ExamSession::create([
            'title' => 'Ujian Evaluasi Demo',
            'subject_id' => $subjects['Bahasa Indonesia']->id,
            'exam_package_id' => $pkg->id,
            'exam_type_id' => $type->id,
            'start_time' => now()->subHours(1),
            'end_time' => now()->addDays(2),
            'duration' => 60,
            'description' => 'Silakan kerjakan ujian ini untuk demo.',
            'is_active' => true,
            'token' => 'DEMO1'
            // 'user_id' => $teacher->id // removed as it doesn't exist
        ]);

        $this->command->info("Selesai! \nAdmin: sekolah@demo.com | Pass: 12345678 \nGuru: guru@demo.com | Pass: 12345678 \nSiswa: 1001 (NIS) / siswa1@demo.com | Pass: 12345678");
    }
}
