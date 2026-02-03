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
            // Drop global unique index on EMAIL
            // Using exception handling in case it doesn't exist
            try {
                $table->dropUnique(['email']);
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
            
            // Add composite unique index (EMAIL + Created By)
            $table->unique(['email', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['email', 'created_by']);
            $table->unique('email');
        });
    }
};
