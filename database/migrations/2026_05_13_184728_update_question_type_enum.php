<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `questions`
            MODIFY COLUMN `type`
            ENUM('multiple_choice', 'multiple_choice_complex', 'boolean_grid', 'essay')
            NOT NULL
            DEFAULT 'multiple_choice'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE `questions`
            MODIFY COLUMN `type`
            ENUM('multiple_choice', 'essay')
            NOT NULL
            DEFAULT 'multiple_choice'
        ");
    }
};