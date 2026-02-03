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
        Schema::create('score_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_group_id')->constrained()->onDelete('cascade');
            $table->integer('correct_count');
            $table->decimal('scaled_score', 8, 2);
            $table->timestamps();

            $table->unique(['institution_id', 'question_group_id', 'correct_count'], 'unique_scale_rule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('score_scales');
    }
};
