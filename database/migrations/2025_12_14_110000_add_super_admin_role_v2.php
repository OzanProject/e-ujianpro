<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add super_admin to the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_lembaga', 'pengajar', 'peserta_ujian', 'operator', 'super_admin') NOT NULL DEFAULT 'peserta_ujian'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 
        DB::statement("DELETE FROM users WHERE role = 'super_admin'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_lembaga', 'pengajar', 'peserta_ujian', 'operator') NOT NULL DEFAULT 'peserta_ujian'");
    }
};
