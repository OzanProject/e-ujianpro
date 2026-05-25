<?php

namespace App\Imports;

use App\Jobs\ImportStudentsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;

class StudentsImportQueued implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{
    protected $studentGroupId;
    protected $userId;
    protected $maxStudents;

    public function __construct($studentGroupId = null)
    {
        $this->studentGroupId = $studentGroupId;
        $this->userId = auth()->id();
        $this->maxStudents = auth()->user()->max_students;
    }

    /**
     * Process collection in chunks
     */
    public function collection(Collection $rows)
    {
        // Convert collection to array and dispatch job
        $rowsArray = $rows->toArray();
        
        ImportStudentsJob::dispatch(
            $rowsArray,
            $this->studentGroupId,
            $this->userId,
            $this->maxStudents
        );
    }

    /**
     * Process 50 rows at a time
     */
    public function chunkSize(): int
    {
        return 50;
    }
}
