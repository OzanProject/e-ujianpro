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
        Schema::create('exam_session_proctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // The teacher
            $table->timestamps();

            // Prevent assigning multiple proctors to the same session & room? 
            // Actually, maybe two teachers can supervise a big room. Let's not make it strictly unique.
            // But we should prevent the *same* teacher being assigned to the same room/session twice.
            $table->unique(['exam_session_id', 'exam_room_id', 'user_id'], 'esp_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_session_proctors');
    }
};
