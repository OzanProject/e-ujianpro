<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Institution;
use App\Models\StudentGroup;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ExamPackage;
use App\Models\ExamType;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;

class ASATSeeder extends Seeder
{
    /**
     * Seeder data dummy siap pakai untuk ASAT
     * (Asesmen Sumatif Akhir Semester)
     *
     * ⚠  Seeder ini akan MENGHAPUS SEMUA data lama terlebih dahulu
     *    (kecuali akun super_admin), lalu mengisi 1 ujian ASAT saja.
     *
     * Akun yang dibuat:
     *   Admin : asat.admin@sekolah.com  | pass: asat1234
     *   Siswa : NIS 1001 s/d 1002       | pass: siswa1234
     *
     * Ujian : ASAT Bahasa Indonesia Kelas X
     *         25 Soal PG   (kunci: opsi A selang-seling dengan B/C)
     *          5 Soal Esai
     *         Durasi 90 menit, mulai HARI INI pukul 08:00
     *         Token : ASAT2025
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────
        // 0. BERSIHKAN SEMUA DATA LAMA
        //    (kecuali akun super_admin)
        // ─────────────────────────────────────────────
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('   ASATSeeder — Mulai proses seeding   ');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('▶ [0/7] Membersihkan semua data lama...');

        // Matikan foreign key check sementara agar bisa truncate bebas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        ExamAnswer::truncate();
        ExamAttempt::truncate();
        ExamSession::truncate();
        DB::table('exam_package_question')->truncate();
        ExamPackage::truncate();
        QuestionOption::truncate();
        Question::truncate();
        Subject::truncate();
        ExamType::truncate();
        Student::truncate();
        StudentGroup::truncate();
        Institution::truncate();

        // Hapus semua user KECUALI super_admin
        User::where('role', '!=', 'super_admin')->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('   ↳ Semua data lama berhasil dihapus (super_admin dipertahankan)');

        // ─────────────────────────────────────────────
        // 1. ADMIN LEMBAGA
        // ─────────────────────────────────────────────
        $this->command->info('▶ [1/7] Membuat Admin Lembaga...');

        $admin = User::firstOrCreate(
            ['email' => 'asat.admin@sekolah.com'],
            [
                'name'         => 'Admin SMPN 1 Demo',
                'password'     => Hash::make('asat1234'),
                'role'         => 'admin_lembaga',
                'status'       => 'active',
                'max_students' => 200,
                'points_balance' => 99999,
            ]
        );

        // ─────────────────────────────────────────────
        // 2. INSTITUSI
        // ─────────────────────────────────────────────
        $this->command->info('▶ [2/7] Membuat Data Institusi...');

        Institution::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'name'            => 'SMPN 1 Demo',
                'subdomain'       => 'smpn1demo',
                'email'           => 'asat.admin@sekolah.com',
                'phone'           => '0811-2233-4455',
                'address'         => 'Jl. Pendidikan Raya No. 17, Kota Demo',
                'city'            => 'Kota Demo',
                'type'            => 'SMP/Sederajat',
                'head_master'     => 'Drs. Ahmad Fauzi, M.Pd',
                'nip_head_master' => '197501012005011008',
            ]
        );

        // ─────────────────────────────────────────────
        // 3. KELAS / STUDENT GROUP
        // ─────────────────────────────────────────────
        $this->command->info('▶ [3/7] Membuat Kelas (Student Group)...');

        $kelas = StudentGroup::firstOrCreate(
            ['name' => 'X-A', 'created_by' => $admin->id]
        );

        // ─────────────────────────────────────────────
        // 4. SISWA (2 Siswa)
        // ─────────────────────────────────────────────
        $this->command->info('▶ [4/7] Membuat 2 Data Siswa (Uji Coba)...');

        $namaLakiLaki = [
            'Achmad Rizky Maulana',
            'Bagas Dwi Prasetyo',
            'Dimas Satria Nugroho',
            'Fajar Kurniawan',
            'Gilang Ramadan',
            'Hendra Setiawan',
            'Ivan Permana',
            'Jaka Wibowo',
        ];

        $namaPerempuan = [
            'Annisa Rahmawati',
            'Bunga Citra Lestari',
            'Dewi Sartika Putri',
            'Eka Fitriani',
            'Fina Aulia Sari',
            'Gita Nirmala',
            'Hana Permatasari',
        ];

        $semuaNama = array_slice(array_merge($namaLakiLaki, $namaPerempuan), 0, 2);

        foreach ($semuaNama as $index => $nama) {
            $nis = '10' . str_pad($index + 1, 2, '0', STR_PAD_LEFT); // 1001 - 1015

            Student::updateOrCreate(
                ['nis' => $nis, 'created_by' => $admin->id],
                [
                    'name'             => $nama,
                    'email'            => 'siswa.' . $nis . '@sekolah.com',
                    'password'         => Hash::make('siswa1234'),
                    'kelas'            => 'X-A',
                    'jurusan'          => '-',
                    'student_group_id' => $kelas->id,
                    'phone_number'     => '0812' . str_pad($nis, 8, '0', STR_PAD_LEFT),
                ]
            );
        }

        $this->command->info('   ↳ 2 siswa berhasil dibuat (NIS 1001 - 1002)');

        // ─────────────────────────────────────────────
        // 5. MATA PELAJARAN
        // ─────────────────────────────────────────────
        $this->command->info('▶ [5/7] Membuat Mata Pelajaran...');

        $mapel = Subject::updateOrCreate(
            ['name' => 'Bahasa Indonesia', 'created_by' => $admin->id],
            ['code' => 'BINDO-X']
        );

        // ─────────────────────────────────────────────
        // 6. BANK SOAL & PAKET UJIAN
        // ─────────────────────────────────────────────
        $this->command->info('▶ [6/7] Membuat Bank Soal & Paket Ujian ASAT...');

        $paket = ExamPackage::create([
            'subject_id' => $mapel->id,
            'name'       => 'Paket ASAT — Bahasa Indonesia Kelas X',
            'code'       => 'ASAT-BINDO-X-2025',
            'created_by' => $admin->id,
        ]);

        // --- 25 SOAL PILIHAN GANDA ---
        $soalPG = [
            // ── Teks Narasi ──
            [
                'content' => '<p><strong>Bacalah teks berikut!</strong></p>
<p>Setiap pagi, Pak Budi pergi ke pasar membeli sayuran. Ia selalu memilih sayuran segar agar keluarganya dapat menikmati makanan bergizi. Istrinya memasak dengan penuh kasih sayang sehingga anak-anaknya selalu rindu masakan rumah.</p>
<p>Apa gagasan pokok paragraf di atas?</p>',
                'options' => [
                    ['content' => 'Kebiasaan Pak Budi membeli sayuran segar setiap pagi', 'is_correct' => true],
                    ['content' => 'Istri Pak Budi memasak dengan penuh kasih sayang', 'is_correct' => false],
                    ['content' => 'Anak-anak Pak Budi selalu rindu masakan rumah', 'is_correct' => false],
                    ['content' => 'Sayuran segar sangat bergizi untuk keluarga', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Dalam teks narasi di atas, kata <em>"bergizi"</em> memiliki makna...</p>',
                'options' => [
                    ['content' => 'Mengandung zat yang bermanfaat bagi tubuh', 'is_correct' => false],
                    ['content' => 'Makanan yang banyak bumbu dan rasa', 'is_correct' => true],
                    ['content' => 'Sayuran yang berwarna hijau', 'is_correct' => false],
                    ['content' => 'Bahan makanan yang mahal harganya', 'is_correct' => false],
                ],
            ],
            // ── Jenis Teks ──
            [
                'content' => '<p>Teks yang bertujuan untuk menceritakan suatu kejadian atau peristiwa secara berurutan disebut teks...</p>',
                'options' => [
                    ['content' => 'Narasi', 'is_correct' => true],
                    ['content' => 'Deskripsi', 'is_correct' => false],
                    ['content' => 'Persuasi', 'is_correct' => false],
                    ['content' => 'Eksposisi', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Teks yang menggambarkan suatu objek secara detail sehingga pembaca seolah-olah melihat, merasakan, atau mendengar langsung objek tersebut disebut teks...</p>',
                'options' => [
                    ['content' => 'Eksposisi', 'is_correct' => false],
                    ['content' => 'Deskripsi', 'is_correct' => true],
                    ['content' => 'Narasi', 'is_correct' => false],
                    ['content' => 'Argumentasi', 'is_correct' => false],
                ],
            ],
            // ── EYD / Kaidah Bahasa ──
            [
                'content' => '<p>Pilihlah penulisan kata yang baku!</p>',
                'options' => [
                    ['content' => 'Analisa', 'is_correct' => false],
                    ['content' => 'Analisis', 'is_correct' => true],
                    ['content' => 'Analisi', 'is_correct' => false],
                    ['content' => 'Analésis', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Kalimat manakah yang menggunakan tanda baca yang benar?</p>',
                'options' => [
                    ['content' => 'Rina pergi ke pasar, dan Dina tetap di rumah.', 'is_correct' => false],
                    ['content' => 'Rina pergi ke pasar dan Dina tetap di rumah.', 'is_correct' => true],
                    ['content' => 'Rina pergi ke pasar. dan Dina tetap di rumah.', 'is_correct' => false],
                    ['content' => 'Rina pergi, ke pasar dan Dina tetap di rumah.', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Penulisan kata depan <strong>"di"</strong> yang benar terdapat pada kalimat...</p>',
                'options' => [
                    ['content' => 'Buku itu diletakkan dimeja belajar.', 'is_correct' => false],
                    ['content' => 'Buku itu di letakkan di meja belajar.', 'is_correct' => false],
                    ['content' => 'Buku itu diletakkan di meja belajar.', 'is_correct' => true],
                    ['content' => 'Buku itu di letakkan dimeja belajar.', 'is_correct' => false],
                ],
            ],
            // ── Paragraf & Kalimat Utama ──
            [
                'content' => '<p>Kalimat yang memuat gagasan pokok dalam sebuah paragraf disebut...</p>',
                'options' => [
                    ['content' => 'Kalimat utama', 'is_correct' => true],
                    ['content' => 'Kalimat penjelas', 'is_correct' => false],
                    ['content' => 'Kalimat penutup', 'is_correct' => false],
                    ['content' => 'Kalimat majemuk', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Paragraf yang kalimat utamanya terletak di akhir paragraf disebut paragraf...</p>',
                'options' => [
                    ['content' => 'Deduktif', 'is_correct' => false],
                    ['content' => 'Induktif', 'is_correct' => true],
                    ['content' => 'Campuran', 'is_correct' => false],
                    ['content' => 'Naratif', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Paragraf yang kalimat utamanya berada di awal paragraf disebut paragraf...</p>',
                'options' => [
                    ['content' => 'Deduktif', 'is_correct' => true],
                    ['content' => 'Induktif', 'is_correct' => false],
                    ['content' => 'Deskriptif', 'is_correct' => false],
                    ['content' => 'Naratif', 'is_correct' => false],
                ],
            ],
            // ── Teks Prosedur ──
            [
                'content' => '<p>Ciri utama teks prosedur adalah...</p>',
                'options' => [
                    ['content' => 'Berisi langkah-langkah atau cara melakukan sesuatu secara berurutan', 'is_correct' => false],
                    ['content' => 'Menggunakan kalimat perintah dan konjungsi urutan waktu', 'is_correct' => true],
                    ['content' => 'Menggambarkan suasana alam dengan detail', 'is_correct' => false],
                    ['content' => 'Memuat pendapat penulis dan argumen pendukung', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Salah satu contoh konjungsi urutan yang sering dipakai dalam teks prosedur adalah...</p>',
                'options' => [
                    ['content' => 'Akan tetapi', 'is_correct' => false],
                    ['content' => 'Kemudian', 'is_correct' => true],
                    ['content' => 'Sehingga', 'is_correct' => false],
                    ['content' => 'Meskipun', 'is_correct' => false],
                ],
            ],
            // ── Puisi ──
            [
                'content' => '<p>Dalam puisi, rima yang terdapat pada akhir setiap baris disebut...</p>',
                'options' => [
                    ['content' => 'Rima akhir', 'is_correct' => true],
                    ['content' => 'Rima awal', 'is_correct' => false],
                    ['content' => 'Rima dalam', 'is_correct' => false],
                    ['content' => 'Ritme', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Bait dalam puisi adalah...</p>',
                'options' => [
                    ['content' => 'Sekumpulan kata dalam satu baris puisi', 'is_correct' => false],
                    ['content' => 'Kelompok baris dalam puisi yang membentuk kesatuan makna', 'is_correct' => true],
                    ['content' => 'Keseluruhan isi puisi dalam satu halaman', 'is_correct' => false],
                    ['content' => 'Judul puisi yang ditulis di bagian atas', 'is_correct' => false],
                ],
            ],
            // ── Kosakata ──
            [
                'content' => '<p>Sinonim kata <strong>"antusias"</strong> adalah...</p>',
                'options' => [
                    ['content' => 'Malas', 'is_correct' => false],
                    ['content' => 'Bersemangat', 'is_correct' => true],
                    ['content' => 'Sedih', 'is_correct' => false],
                    ['content' => 'Bingung', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Antonim kata <strong>"optimis"</strong> adalah...</p>',
                'options' => [
                    ['content' => 'Pesimis', 'is_correct' => false],
                    ['content' => 'Yakin', 'is_correct' => false],
                    ['content' => 'Kuat', 'is_correct' => true],  // sengaja diubah biar tidak mudah
                    ['content' => 'Percaya diri', 'is_correct' => false],
                ],
            ],
            // ── Surat ──
            [
                'content' => '<p>Bagian surat resmi yang berisi maksud dan tujuan penulisan surat disebut...</p>',
                'options' => [
                    ['content' => 'Pembukaan surat', 'is_correct' => false],
                    ['content' => 'Isi surat', 'is_correct' => true],
                    ['content' => 'Penutup surat', 'is_correct' => false],
                    ['content' => 'Lampiran surat', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Salam pembuka yang tepat pada surat resmi adalah...</p>',
                'options' => [
                    ['content' => 'Dengan hormat,', 'is_correct' => true],
                    ['content' => 'Halo, apa kabar?', 'is_correct' => false],
                    ['content' => 'Salam sejahtera untuk kita semua,', 'is_correct' => false],
                    ['content' => 'Kepada yang terhormat,', 'is_correct' => false],
                ],
            ],
            // ── Cerpen ──
            [
                'content' => '<p>Unsur intrinsik cerpen yang menggambarkan watak dan karakter tokoh disebut...</p>',
                'options' => [
                    ['content' => 'Penokohan', 'is_correct' => false],
                    ['content' => 'Alur', 'is_correct' => false],
                    ['content' => 'Karakter', 'is_correct' => true],
                    ['content' => 'Sudut pandang', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Tahapan alur cerita setelah konflik mencapai puncaknya disebut...</p>',
                'options' => [
                    ['content' => 'Pengenalan', 'is_correct' => false],
                    ['content' => 'Klimaks', 'is_correct' => false],
                    ['content' => 'Penyelesaian (resolusi)', 'is_correct' => true],
                    ['content' => 'Komplikasi', 'is_correct' => false],
                ],
            ],
            // ── Teks Eksposisi ──
            [
                'content' => '<p>Teks eksposisi bertujuan untuk...</p>',
                'options' => [
                    ['content' => 'Meyakinkan pembaca untuk mengikuti pendapat penulis', 'is_correct' => false],
                    ['content' => 'Memaparkan dan menjelaskan informasi secara objektif', 'is_correct' => true],
                    ['content' => 'Menceritakan kejadian secara kronologis', 'is_correct' => false],
                    ['content' => 'Menggambarkan keindahan suatu tempat', 'is_correct' => false],
                ],
            ],
            // ── Majas ──
            [
                'content' => '<p>Kalimat <em>"Angin berbisik lembut di antara dedaunan"</em> menggunakan majas...</p>',
                'options' => [
                    ['content' => 'Hiperbola', 'is_correct' => false],
                    ['content' => 'Personifikasi', 'is_correct' => true],
                    ['content' => 'Simile', 'is_correct' => false],
                    ['content' => 'Metafora', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Kalimat <em>"Badannya sekeras batu karang"</em> menggunakan majas...</p>',
                'options' => [
                    ['content' => 'Personifikasi', 'is_correct' => false],
                    ['content' => 'Simile', 'is_correct' => false],
                    ['content' => 'Metafora', 'is_correct' => true],
                    ['content' => 'Hiperbola', 'is_correct' => false],
                ],
            ],
            // ── Teks Laporan ──
            [
                'content' => '<p>Struktur teks laporan hasil observasi yang benar adalah...</p>',
                'options' => [
                    ['content' => 'Pernyataan umum → Aspek yang dilaporkan → Kesimpulan', 'is_correct' => true],
                    ['content' => 'Tesis → Argumen → Penegasan ulang', 'is_correct' => false],
                    ['content' => 'Orientasi → Komplikasi → Resolusi', 'is_correct' => false],
                    ['content' => 'Tujuan → Langkah-langkah → Penutup', 'is_correct' => false],
                ],
            ],
            [
                'content' => '<p>Dalam teks laporan hasil observasi, bagian yang berisi gambaran umum tentang objek yang dilaporkan disebut...</p>',
                'options' => [
                    ['content' => 'Definisi umum', 'is_correct' => false],
                    ['content' => 'Pernyataan umum', 'is_correct' => true],
                    ['content' => 'Deskripsi khusus', 'is_correct' => false],
                    ['content' => 'Kesimpulan observasi', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($soalPG as $idx => $soalData) {
            $question = Question::create([
                'subject_id' => $mapel->id,
                'type'       => 'multiple_choice',
                'content'    => $soalData['content'],
            ]);

            foreach ($soalData['options'] as $optData) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'content'     => $optData['content'],
                    'is_correct'  => $optData['is_correct'],
                ]);
            }

            $paket->questions()->attach($question->id);
        }

        $this->command->info('   ↳ 25 Soal Pilihan Ganda berhasil dibuat');

        // --- 5 SOAL ESAI ---
        $soalEsai = [
            '<p><strong>Soal Esai No. 1</strong><br>Jelaskan perbedaan antara paragraf deduktif dan paragraf induktif! Berikan masing-masing satu contoh kalimat utamanya!</p>',
            '<p><strong>Soal Esai No. 2</strong><br>Tuliskan sebuah paragraf deskriptif singkat (minimal 4 kalimat) tentang suasana kelas kalian saat pagi hari!</p>',
            '<p><strong>Soal Esai No. 3</strong><br>Sebutkan dan jelaskan minimal <strong>3 unsur intrinsik</strong> yang terdapat dalam sebuah cerpen!</p>',
            '<p><strong>Soal Esai No. 4</strong><br>Apa yang dimaksud dengan majas personifikasi? Berikan 2 contoh kalimat yang menggunakan majas personifikasi!</p>',
            '<p><strong>Soal Esai No. 5</strong><br>Tuliskan struktur teks prosedur beserta penjelasan singkat dan contoh nyata dari setiap bagiannya!</p>',
        ];

        foreach ($soalEsai as $esaiContent) {
            $essay = Question::create([
                'subject_id' => $mapel->id,
                'type'       => 'essay',
                'content'    => $esaiContent,
            ]);
            $paket->questions()->attach($essay->id);
        }

        $this->command->info('   ↳ 5 Soal Esai berhasil dibuat');

        // ─────────────────────────────────────────────
        // 7. JADWAL UJIAN (EXAM SESSION)
        // ─────────────────────────────────────────────
        $this->command->info('▶ [7/7] Membuat Sesi Ujian ASAT...');

        $examType = ExamType::firstOrCreate(
            ['name' => 'ASAT', 'created_by' => $admin->id],
            [
                'description' => 'Asesmen Sumatif Akhir Semester',
                'is_active'   => true,
            ]
        );

        // Mulai pukul 08:00 hari ini, berakhir pukul 10:00
        $today    = now()->format('Y-m-d');
        $startTime = $today . ' 08:00:00';
        $endTime   = $today . ' 10:00:00';

        $session = ExamSession::create([
            'title'          => 'ASAT Bahasa Indonesia — Kelas X T.A. 2024/2025',
            'subject_id'     => $mapel->id,
            'exam_package_id'=> $paket->id,
            'exam_type_id'   => $examType->id,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'duration'       => 90,
            'description'    => 'Asesmen Sumatif Akhir Semester (ASAT) mata pelajaran Bahasa Indonesia untuk Kelas X. Kerjakan dengan jujur dan penuh tanggung jawab. Dilarang membuka buku atau catatan selama ujian berlangsung.',
            'is_active'      => true,
            'show_score'     => true,
            'token'          => 'ASAT2025',
        ]);

        // ─────────────────────────────────────────────
        // RINGKASAN
        // ─────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('   ✅  ASATSeeder SELESAI — Ringkasan Data             ');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  Admin    : asat.admin@sekolah.com  | pass: asat1234  ');
        $this->command->info('  Siswa    : NIS 1001 s/d 1002       | pass: siswa1234 ');
        $this->command->info('  Kelas    : X-A (2 siswa)                            ');
        $this->command->info('  Mapel    : Bahasa Indonesia                          ');
        $this->command->info('  Paket    : ASAT-BINDO-X-2025                         ');
        $this->command->info('  Soal     : 25 PG + 5 Esai = 30 soal                  ');
        $this->command->info("  Sesi     : {$session->title}");
        $this->command->info("  Waktu    : $startTime  s/d  $endTime");
        $this->command->info('  Durasi   : 90 menit                                  ');
        $this->command->info('  Token    : ASAT2025                                   ');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('  Jalankan ujian di: http://127.0.0.1:8000/siswa/login ');
        $this->command->info('  Panel admin di   : http://127.0.0.1:8000/login       ');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
