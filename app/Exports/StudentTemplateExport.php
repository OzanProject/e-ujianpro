<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class StudentTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return new Collection([
            ['Budi Santoso', '12345', '0012345678', 'L', 'X-IPA', 'IPA', 'password123', 'Kelompok 1', 'Ruang Ujian 1', '02-15-0159-001-9'],
            ['Siti Aminah', '12346', '', 'P', 'X-IPA', 'IPA', '', '', '', '02-15-0159-002-8']
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIS',
            'NISN (Opsional)',
            'Jenis Kelamin (L/P)',
            'Kelas',
            'Jurusan',
            'Password (Opsional)',
            'Kelompok (Opsional)',
            'Ruangan (Opsional)',
            'NO PESERTA'
        ];
    }
}
