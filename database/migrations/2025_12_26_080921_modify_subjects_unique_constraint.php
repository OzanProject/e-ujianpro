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
        Schema::table('subjects', function (Blueprint $table) {
            // Drop existing global unique index on code
            $table->dropUnique(['code']);
            
            // Add new composite unique index scoped by created_by
            // Meaning: Code 'MTK' is unique only for user_id 1. User 2 can also have 'MTK'.
            $table->unique(['code', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['code', 'created_by']);
            $table->unique('code');
        });
    }
};
