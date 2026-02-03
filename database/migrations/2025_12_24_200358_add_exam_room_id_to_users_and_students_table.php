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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_room_id')->nullable()->after('role');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_room_id')->nullable()->after('student_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('exam_room_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('exam_room_id');
        });
    }
};
