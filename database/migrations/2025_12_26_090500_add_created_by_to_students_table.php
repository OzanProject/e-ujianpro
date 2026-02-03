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
            $table->unsignedBigInteger('created_by')->nullable()->after('user_id');
            // Adding index to speed up scoped queries
            $table->index('created_by');
        });

        // Optional: Backfill logic could go here, but safer to do in a seeder or manual command
        // if user_id was previously used confusingly. 
        // Given the code analysis, existing students might be orphaned or relying on group relationship.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
