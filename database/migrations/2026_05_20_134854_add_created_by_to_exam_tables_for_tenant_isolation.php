<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan kolom created_by untuk isolasi tenant pada tabel:
     * - exam_sessions
     * - exam_attempts
     * - exam_answers
     * - reading_texts
     */
    public function up(): void
    {
        // Add created_by to exam_sessions
        if (!Schema::hasColumn('exam_sessions', 'created_by')) {
            Schema::table('exam_sessions', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->after('exam_type_id');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->index('created_by');
            });
        }

        // Add created_by to exam_attempts
        if (!Schema::hasColumn('exam_attempts', 'created_by')) {
            Schema::table('exam_attempts', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->after('cheat_count');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->index('created_by');
            });
        }

        // Add created_by to exam_answers
        if (!Schema::hasColumn('exam_answers', 'created_by')) {
            Schema::table('exam_answers', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_doubtful');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->index('created_by');
            });
        }

        // Add created_by to reading_texts
        if (!Schema::hasColumn('reading_texts', 'created_by')) {
            Schema::table('reading_texts', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->after('content');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->index('created_by');
            });
        }

        // Populate created_by for existing records
        $this->populateCreatedBy();
    }

    /**
     * Populate created_by for existing records based on relationships
     */
    private function populateCreatedBy(): void
    {
        // Update exam_sessions based on subject's created_by
        DB::statement('
            UPDATE exam_sessions es
            INNER JOIN subjects s ON es.subject_id = s.id
            SET es.created_by = s.created_by
            WHERE es.created_by IS NULL AND s.created_by IS NOT NULL
        ');

        // Update exam_attempts based on student's created_by
        DB::statement('
            UPDATE exam_attempts ea
            INNER JOIN students st ON ea.student_id = st.id
            SET ea.created_by = st.created_by
            WHERE ea.created_by IS NULL AND st.created_by IS NOT NULL
        ');

        // Update exam_answers based on exam_attempt's created_by
        DB::statement('
            UPDATE exam_answers ea
            INNER JOIN exam_attempts eat ON ea.exam_attempt_id = eat.id
            SET ea.created_by = eat.created_by
            WHERE ea.created_by IS NULL AND eat.created_by IS NOT NULL
        ');

        // Update reading_texts based on subject's created_by
        DB::statement('
            UPDATE reading_texts rt
            INNER JOIN subjects s ON rt.subject_id = s.id
            SET rt.created_by = s.created_by
            WHERE rt.created_by IS NULL AND s.created_by IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('reading_texts', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
