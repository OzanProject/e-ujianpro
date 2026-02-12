<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Institution;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the institution exists if we link it, but Super Admin might not need one strictly, 
        // or can belong to a default 'System' institution.
        // For now, let's just create the user.

        $superAdmin = User::updateOrCreate(
            ['email' => 'ardiansyahdzan@gmail.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $this->command->info("Super Admin Created:");
        $this->command->info("Email: ardiansyahdzan@gmail.com");
        $this->command->info("Password: password");
    }
}
