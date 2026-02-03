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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable(); // Can store JSON
            $table->timestamps();
        });

        // Seed default values
        DB::table('settings')->insert([
            ['key' => 'point_price', 'value' => '675', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bank_accounts', 'value' => json_encode([
                [
                    'bank' => 'BCA',
                    'number' => '1234567890',
                    'name' => 'PT E-Ujian'
                ],
                [
                    'bank' => 'BRI',
                    'number' => '0987654321',
                    'name' => 'PT E-Ujian'
                ]
            ]), 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
