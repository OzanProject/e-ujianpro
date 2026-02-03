<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix Exam Attempts
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Drop foreign key to users
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Add foreign key to students
            $table->foreignId('student_id')->after('exam_session_id')->constrained()->onDelete('cascade');
            
            // Rename total_score to score for consistency
            $table->renameColumn('total_score', 'score');
        });

        // Fix Exam Answers
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->boolean('is_doubtful')->default(false)->after('answer_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn('student_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->renameColumn('score', 'total_score');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropColumn('is_doubtful');
        });
    }
};
