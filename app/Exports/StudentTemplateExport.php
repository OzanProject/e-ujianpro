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
            ['Budi Santoso', '12345', 'X-IPA', 'IPA', 'password123', 'Kelompok 1', 'Ruang Ujian 1'],
            ['Siti Aminah', '12346', 'X-IPA', 'IPA', '', '', '']
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIS',
            'Kelas',
            'Jurusan',
            'Password (Opsional)'
        ];
    }
}
