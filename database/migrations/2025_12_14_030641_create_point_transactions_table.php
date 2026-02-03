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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out'])->default('in'); // Masuk (Topup) atau Keluar (Pakai)
            $table->integer('amount'); // Jumlah poin
            $table->string('description')->nullable(); // Keterangan
            $table->string('status')->default('pending'); // pending, success, failed
            $table->string('reference_id')->nullable(); // No Transaksi / Bukti
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
