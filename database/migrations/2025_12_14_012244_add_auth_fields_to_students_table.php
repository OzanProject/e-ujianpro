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
            $table->string('name')->after('id');
            $table->string('password')->after('name');
            $table->foreignId('user_id')->nullable()->change(); // Make user_id nullable or remove it later
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['name', 'password']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
