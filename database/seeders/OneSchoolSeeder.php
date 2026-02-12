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

        $this->command->info('1. Membuat Akun Admin Sekolah & Institusi...');

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
        $institution = Institution::updateOrCreate(
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

        $this->command->info('2. Membuat Mata Pelajaran & Guru...');

        // 3. Mata Pelajaran
        $subjectsData = [
            'Matematika' => 'MTK',
            'Bahasa Indonesia' => 'BIN',
            'Bahasa Inggris' => 'BIG',
            'Fisika' => 'FIS',
            'Biologi' => 'BIO'
        ];

        $subjects = [];
        foreach ($subjectsData as $name => $code) {
            $subjects[$name] = Subject::updateOrCreate(
                ['name' => $name, 'created_by' => $admin->id],
                ['code' => $code . '-' . rand(100, 999)]
            );
        }

        // 4. Guru
        $teacher = User::firstOrCreate(
            ['email' => 'guru@demo.com'],
            [
                'name' => 'Guru Demo (IPA)',
                'password' => Hash::make('12345678'),
                'role' => 'pengajar',
                'status' => 'active',
                'created_by' => $admin->id,
            ]
        );
        // Assign all subjects to this teacher for demo convenience
        $teacher->subjects()->sync(collect($subjects)->pluck('id'));

        $this->command->info('3. Membuat Kelas & Siswa...');

        // 5. Kelas
        $classNames = ['X IPA 1', 'X IPA 2'];
        $groups = [];
        foreach ($classNames as $className) {
            $groups[$className] = StudentGroup::firstOrCreate(
                ['name' => $className, 'created_by' => $admin->id]
            );
        }

        // 6. Siswa (10 per kelas)
        foreach ($groups as $className => $group) {
            for ($i = 1; $i <= 10; $i++) {
                // Generate NIS unik per sekolah (tergantung created_by)
                $nisSuffix = ($className == 'X IPA 1' ? '10' : '20') . str_pad($i, 2, '0', STR_PAD_LEFT);
                $nis = "2024" . $nisSuffix;

                Student::updateOrCreate(
                    ['nis' => $nis, 'created_by' => $admin->id],
                    [
                        'name' => "Siswa $className No $i",
                        'email' => "siswa.$nis@demo.com",
                        'password' => Hash::make('12345678'),
                        'student_group_id' => $group->id,
                        'kelas' => $group->name,
                        'jurusan' => 'IPA',
                        'phone_number' => '08123456' . $nis
                    ]
                );
            }
        }

        $this->command->info('4. Membuat Bank Soal & Paket Ujian...');

        // 7. Bank Soal & Paket (Untuk setiap mapel)
        foreach ($subjects as $name => $subject) {
            // Buat Paket
            $pkg = ExamPackage::create([
                'subject_id' => $subject->id,
                'name' => "Ujian Akhir Semester $name",
                'created_by' => $admin->id,
                'code' => 'UAS-' . strtoupper(substr($name, 0, 3)) . '-' . date('Y')
            ]);

            // Buat 10 Soal PG
            for ($q = 1; $q <= 10; $q++) {
                $question = Question::create([
                    'subject_id' => $subject->id,
                    'type' => 'multiple_choice',
                    'content' => "<p>Soal $name Nomor $q. Pilihlah jawaban yang paling benar!</p>",
                ]);

                // Opsi
                foreach (['A', 'B', 'C', 'D', 'E'] as $idx => $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'content' => "Pilihan $opt untuk soal $q",
                        'is_correct' => ($idx == 0) // Kunci A
                    ]);
                }

                // Masukkan ke paket
                $pkg->questions()->attach($question->id);
            }

            // Buat 5 Soal Essay
            for ($e = 1; $e <= 5; $e++) {
                $essay = Question::create([
                    'subject_id' => $subject->id,
                    'type' => 'essay',
                    'content' => "<p><strong>Soal Esai No. $e:</strong><br>Jelaskan secara rinci konsep evaluasi $name pada topik ke-$e, berikan contoh kasus nyata!</p>",
                ]);
                $pkg->questions()->attach($essay->id);
            }
            $this->command->info("   - $name: 10 Soal PG & 5 Soal Essay dibuat.");

            // 8. Jadwalkan Ujian (Sesi)
            $type = ExamType::firstOrCreate(
                ['name' => 'UAS', 'created_by' => $admin->id],
                ['description' => 'Ujian Akhir Semester', 'is_active' => true]
            );

            // Tentukan jadwal: 2 Mapel per hari
            // Mapel 1: 08:00 - 09:30
            // Mapel 2: 10:00 - 11:30

            // Cari index mapel saat ini utk menentukan urutan
            $subjectKeys = array_keys($subjects);
            $index = array_search($name, $subjectKeys); // 0, 1, 2, 3, 4

            $dayOffset = floor($index / 2); // 0, 0, 1, 1, 2
            $timeSlot = $index % 2; // 0 (Pagi), 1 (Siang)

            // Mulai besok
            $date = now()->addDays(1)->addDays($dayOffset)->format('Y-m-d');

            if ($timeSlot == 0) {
                $start = "$date 08:00:00";
                $end = "$date 09:30:00";
            } else {
                $start = "$date 10:00:00";
                $end = "$date 11:30:00";
            }

            ExamSession::create([
                'title' => "UAS $name T.A. 2024/2025",
                'subject_id' => $subject->id,
                'exam_package_id' => $pkg->id,
                'exam_type_id' => $type->id,
                'start_time' => $start,
                'end_time' => $end, // Waktu sesi berakhir (batas akses)
                'duration' => 90,
                'description' => "Jadwal: " . \Carbon\Carbon::parse($start)->translatedFormat('l, d F Y H:i') . " WIB",
                'is_active' => true,
                'token' => strtoupper(substr($name, 0, 3)) . '123'
            ]);
        }

        $this->command->info("Selesai! \n---------------------------------------------");
        $this->command->info("Admin: sekolah@demo.com | Pass: 12345678");
        $this->command->info("Guru: guru@demo.com | Pass: 12345678");
        $this->command->info("Siswa: NIS 20241001 (untuk X IPA 1) | Pass: 12345678");
        $this->command->info("---------------------------------------------");
    }
}

