<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Institution::create([
            'name' => 'SMK Indonesia Maju',
            'email' => 'info@smkindonesia.sch.id',
            'phone' => '021-12345678',
            'address' => 'Jl. Pendidikan No. 1, Jakarta',
            'head_master' => 'Dr. H. Ahmad Fauzi, M.Pd.',
            'nip_head_master' => '19800101 200501 1 001'
        ]);
        
        $this->command->info('Institution Data Seeded.');
    }
}
