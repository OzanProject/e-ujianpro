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
        // Add created_by to subjects
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->index()->after('id');
        });

        // Add created_by to student_groups
        Schema::table('student_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->index()->after('id');
        });
        
        // Add created_by to exam_packages just in case (though it has subject_id, ownership is safer on package too)
        // Actually exam_packages often links to subject. Let's check if it needs direct ownership.
        // Usually Package belongs to User who made it.
         Schema::table('exam_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->index()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        
        Schema::table('student_groups', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        
         Schema::table('exam_packages', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
