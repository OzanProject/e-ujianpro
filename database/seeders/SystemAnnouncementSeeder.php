<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemAnnouncement;

class SystemAnnouncementSeeder extends Seeder
{
    public function run()
    {
        SystemAnnouncement::create([
            'title' => 'Deep Delete Implemented',
            'content' => 'Fitur penghapusan bersih (Deep Delete) kini aktif. Menghapus sekolah akan menghapus seluruh data terkait (Siswa, Guru, Mapel, dll).',
            'type' => 'feature',
            'created_at' => now(),
        ]);

        SystemAnnouncement::create([
            'title' => 'Dashboard Dinamis',
            'content' => 'Halaman Dashboard Admin Lembaga kini menampilkan data realtime dan statistik akurat.',
            'type' => 'info',
            'created_at' => now()->subDay(),
        ]);
        
        SystemAnnouncement::create([
            'title' => 'Perbaikan Zona Waktu',
            'content' => 'Sistem kini menggunakan Waktu Indonesia Barat (Asia/Jakarta) secara default.',
            'type' => 'maintenance',
            'created_at' => now()->subDays(2),
        ]);
    }
}
