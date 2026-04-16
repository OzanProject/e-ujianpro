<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Hierarki Seeder:
     *   1. SuperAdminSeeder        — Akun super admin (selalu dijalankan)
     *   2. ASATSeeder              — Data demo 1 ujian ASAT, BERSIHKAN data lama otomatis
     *   3. SystemAnnouncementSeeder — (Opsional) Pengumuman sistem
     *
     * Seeder yang sudah DEPRECATED (jangan diaktifkan):
     *   - OneSchoolSeeder   : membuat 5 ujian sekaligus, sudah digantikan ASATSeeder
     *   - DummyDataSeeder   : data acak tanpa struktur SAAS, tidak relevan
     *   - AdminSeeder       : tidak ada institusi, akan error
     *   - InstitutionSeeder : tidak ada user_id, akan error
     *   - QuestionSeeder    : standalone, tidak link ke admin
     *   - ExamSeeder        : tidak ada created_by, tidak relevan
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,         // ✅ Akun super admin
            ASATSeeder::class,               // ✅ 1 Ujian ASAT — bersihkan data lama otomatis
            // SystemAnnouncementSeeder::class, // ⬜ Opsional: pengumuman sistem
        ]);
    }
}

