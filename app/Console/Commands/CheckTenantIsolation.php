<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTenantIsolation extends Command
{
    protected $signature = 'tenant:check-isolation';
    protected $description = 'Memeriksa integritas isolasi data antar institusi (tenant)';

    public function handle()
    {
        $this->info('🔍 Memeriksa Isolasi Tenant...');
        $this->newLine();

        $issues = [];

        // Check 1: Questions tanpa created_by
        $questionsWithoutOwner = DB::table('questions')
            ->whereNull('created_by')
            ->count();

        if ($questionsWithoutOwner > 0) {
            $issues[] = "❌ {$questionsWithoutOwner} soal tidak memiliki created_by";
            $this->error("❌ {$questionsWithoutOwner} soal tidak memiliki created_by");
        } else {
            $this->info("✅ Semua soal memiliki created_by");
        }

        // Check 2: ExamSessions tanpa created_by
        if (DB::getSchemaBuilder()->hasColumn('exam_sessions', 'created_by')) {
            $sessionsWithoutOwner = DB::table('exam_sessions')
                ->whereNull('created_by')
                ->count();

            if ($sessionsWithoutOwner > 0) {
                $issues[] = "❌ {$sessionsWithoutOwner} sesi ujian tidak memiliki created_by";
                $this->error("❌ {$sessionsWithoutOwner} sesi ujian tidak memiliki created_by");
            } else {
                $this->info("✅ Semua sesi ujian memiliki created_by");
            }
        } else {
            $this->warn("⚠️  Kolom created_by belum ada di tabel exam_sessions");
            $issues[] = "⚠️  Kolom created_by belum ada di tabel exam_sessions";
        }

        // Check 3: ExamAttempts tanpa created_by
        if (DB::getSchemaBuilder()->hasColumn('exam_attempts', 'created_by')) {
            $attemptsWithoutOwner = DB::table('exam_attempts')
                ->whereNull('created_by')
                ->count();

            if ($attemptsWithoutOwner > 0) {
                $issues[] = "❌ {$attemptsWithoutOwner} percobaan ujian tidak memiliki created_by";
                $this->error("❌ {$attemptsWithoutOwner} percobaan ujian tidak memiliki created_by");
            } else {
                $this->info("✅ Semua percobaan ujian memiliki created_by");
            }
        } else {
            $this->warn("⚠️  Kolom created_by belum ada di tabel exam_attempts");
            $issues[] = "⚠️  Kolom created_by belum ada di tabel exam_attempts";
        }

        // Check 4: ExamAnswers tanpa created_by
        if (DB::getSchemaBuilder()->hasColumn('exam_answers', 'created_by')) {
            $answersWithoutOwner = DB::table('exam_answers')
                ->whereNull('created_by')
                ->count();

            if ($answersWithoutOwner > 0) {
                $issues[] = "❌ {$answersWithoutOwner} jawaban ujian tidak memiliki created_by";
                $this->error("❌ {$answersWithoutOwner} jawaban ujian tidak memiliki created_by");
            } else {
                $this->info("✅ Semua jawaban ujian memiliki created_by");
            }
        } else {
            $this->warn("⚠️  Kolom created_by belum ada di tabel exam_answers");
            $issues[] = "⚠️  Kolom created_by belum ada di tabel exam_answers";
        }

        // Check 5: ReadingTexts tanpa created_by
        if (DB::getSchemaBuilder()->hasColumn('reading_texts', 'created_by')) {
            $readingTextsWithoutOwner = DB::table('reading_texts')
                ->whereNull('created_by')
                ->count();

            if ($readingTextsWithoutOwner > 0) {
                $issues[] = "❌ {$readingTextsWithoutOwner} teks bacaan tidak memiliki created_by";
                $this->error("❌ {$readingTextsWithoutOwner} teks bacaan tidak memiliki created_by");
            } else {
                $this->info("✅ Semua teks bacaan memiliki created_by");
            }
        } else {
            $this->warn("⚠️  Kolom created_by belum ada di tabel reading_texts");
            $issues[] = "⚠️  Kolom created_by belum ada di tabel reading_texts";
        }

        // Summary
        $this->newLine();
        if (empty($issues)) {
            $this->info('✅ AMAN: Tidak ada masalah isolasi tenant yang ditemukan!');
            return Command::SUCCESS;
        } else {
            $this->error('⚠️  PERHATIAN: Ditemukan ' . count($issues) . ' masalah!');
            $this->newLine();
            $this->warn('Jalankan migration untuk memperbaiki:');
            $this->line('  php artisan migrate');
            $this->newLine();
            return Command::FAILURE;
        }
    }
}
