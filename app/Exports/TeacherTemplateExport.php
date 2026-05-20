<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class TeacherTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return new Collection([
            ['Ahmad Subarjo', 'ahmad.subarjo@sekolah.sch.id', 'password123', 'Matematika, IPA'],
            ['Siti Aminah', 'siti.aminah@sekolah.sch.id', '', 'Bahasa Indonesia']
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email',
            'Password (Opsional)',
            'Mata Pelajaran (Opsional - Pisahkan dengan koma)'
        ];
    }
}
