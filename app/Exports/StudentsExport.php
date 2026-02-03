<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Student::with('group')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIS',
            'Password',
            'Kelas',
            'Jurusan',
            'Kelompok',
        ];
    }

    public function map($student): array
    {
        return [
            $student->name,
            $student->nis,
            '', // Password masked
            $student->kelas,
            $student->jurusan,
            $student->group->name ?? '',
        ];
    }
}
