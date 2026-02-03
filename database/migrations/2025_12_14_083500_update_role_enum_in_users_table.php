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
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_lembaga', 'pengajar', 'peserta_ujian', 'operator', 'super_admin') NOT NULL DEFAULT 'peserta_ujian'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Warning: This will fail if there are 'operator' or 'super_admin' values in the table.
        DB::statement("DELETE FROM users WHERE role IN ('operator', 'super_admin')"); 
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_lembaga', 'pengajar', 'peserta_ujian') NOT NULL DEFAULT 'peserta_ujian'");
    }
};
