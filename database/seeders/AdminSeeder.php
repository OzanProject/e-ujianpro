<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Lembaga User
        User::updateOrCreate(
            ['email' => 'adminsekolah@gmail.com'],
            [
                'name' => 'Ardiansyah Admin',
                'password' => Hash::make('password'),
                'role' => 'admin_lembaga', // Menggunakan role yang benar untuk akses admin
            ]
        );
    }
}
