<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class StudentsImport implements ToModel, WithHeadingRow
{
    protected $studentGroupId;
    protected $currentCount = 0;
    protected $maxStudents;
    public $skippedCount = 0;
    public $importedCount = 0;
    public $duplicates = [];

    public function __construct($studentGroupId = null)
    {
        $this->studentGroupId = $studentGroupId;
        $this->maxStudents = auth()->user()->max_students;
        $this->currentCount = auth()->user()->students()->count();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip if Name or NIS is empty
        if (empty($row['nama_lengkap']) || empty($row['nis'])) {
            return null;
        }

        $nis = trim($row['nis']);
        
        // 1. Find or Create User
        $user = User::where('email', $nis)->first();
        
        if (!$user) {
            $password = !empty($row['password_opsional']) ? $row['password_opsional'] : $nis;
            $user = User::create([
                'name' => $row['nama_lengkap'],
                'email' => $nis, // Username = NIS
                'password' => Hash::make($password),
                'role' => 'peserta_ujian',
                'created_by' => auth()->id(),
            ]);
        }

        // 2. Find or Create Student linked to User
        $student = Student::where('nis', $nis)->first();

        if ($student) {
            // Found existing student - Track as Duplicate
            $this->duplicates[] = "{$row['nama_lengkap']} ($nis)";
        } else {
            // Check Quota (Local Counter) before creating Student Profile
            if (!is_null($this->maxStudents) && $this->currentCount >= $this->maxStudents) {
                 // Instead of Exception, we skip and count
                 $this->skippedCount++;
                 return null;
            }

            $student = new Student();
            $student->nis = $nis;
            $student->user_id = $user->id;
            $student->created_by = auth()->id(); // Fix: assign creator
            
            // Increment local counter since we are adding a NEW student
            $this->currentCount++;
            $this->importedCount++;
        }

        // 3. Update Attributes (Always update for both New and Existing)
        $student->name = $row['nama_lengkap'];
        $student->email = $user->email;
        $student->password = $user->password; // Keep synced
        $student->kelas = $row['kelas'] ?? $student->kelas;
        $student->jurusan = $row['jurusan'] ?? $student->jurusan;
        
        // Fix: Claim orphaned data if created_by is missing
        if (is_null($student->created_by)) {
            // STRICT QUOTA CHECK FOR CLAIMING
            if (!is_null($this->maxStudents) && $this->currentCount >= $this->maxStudents) {
                // If existing but unclaimed, and quota full, we skip claiming modification but maybe we shouldn't fail the whole row?
                // Logic: If we can't claim, we probably shouldn't return it as "ours".
                $this->skippedCount++;
                return null; 
            }

            $student->created_by = auth()->id();
            $this->currentCount++; // Increment count because we just acquired a new student
        }

        // Only update group if provided/selected
        if ($this->studentGroupId) {
            $student->student_group_id = $this->studentGroupId;
        }

        return $student;
    }

    // Map headings to simplify access (optional, depending on Excel format)
    // The WithHeadingRow concern automatically slugifies headings: "Nama Lengkap" -> "nama_lengkap"
}
