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
        Schema::table('students', function (Blueprint $table) {
            // Drop the global unique index on NIS
            $table->dropUnique(['nis']);
            
            // Add composite unique index (NIS + Created By)
            // This ensures uniqueness only within the same institution
            $table->unique(['nis', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['nis', 'created_by']);
            $table->unique('nis');
        });
    }
};
