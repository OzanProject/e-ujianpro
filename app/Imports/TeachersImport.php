<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class TeachersImport implements ToModel, WithHeadingRow
{
    public $importedCount = 0;
    public $skippedCount = 0;
    public $duplicates = [];
    public $skippedErrors = [];

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Extract row values with fallback key names
        $name = trim($row['nama_lengkap'] ?? $row['nama'] ?? '');
        $email = trim($row['email'] ?? $row['username'] ?? '');
        $passwordOpsional = trim($row['password_opsional'] ?? $row['password'] ?? '');
        
        // Find subject column dynamically (handle variations in naming)
        $subjectsString = '';
        foreach ($row as $key => $val) {
            if (str_contains($key, 'mata_pelajaran') || str_contains($key, 'subject') || str_contains($key, 'mapel')) {
                $subjectsString = trim($val);
                break;
            }
        }

        // Skip if Name or Email is empty
        if (empty($name) || empty($email)) {
            return null;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->skippedCount++;
            $this->skippedErrors[] = "Baris untuk '{$name}': Format email '{$email}' tidak valid.";
            return null;
        }

        // Determine password
        $passwordText = !empty($passwordOpsional) ? $passwordOpsional : '123456';

        // Check if user already exists
        $user = User::where('email', $email)->first();

        if ($user) {
            // Check ownership/institution
            if ($user->created_by != auth()->id()) {
                $this->skippedCount++;
                $this->skippedErrors[] = "Email '{$email}' sudah digunakan oleh lembaga lain.";
                return null;
            }

            // Check role compatibility
            if ($user->role !== 'pengajar') {
                $this->skippedCount++;
                $this->skippedErrors[] = "Email '{$email}' terdaftar dengan hak akses berbeda ({$user->role}).";
                return null;
            }

            // Update existing teacher
            $user->name = $name;
            if (!empty($passwordOpsional)) {
                $user->password = Hash::make($passwordOpsional);
            }
            $user->save();

            $this->duplicates[] = "{$name} ({$email})";
        } else {
            // Create new teacher account
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($passwordText),
                'role' => 'pengajar',
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            $this->importedCount++;
        }

        // Process Subjects association
        if (!empty($subjectsString)) {
            $subjectNames = array_filter(array_map('trim', explode(',', $subjectsString)));
            $subjectIds = [];

            foreach ($subjectNames as $subjName) {
                $subject = $this->findOrCreateSubject($subjName);
                if ($subject) {
                    $subjectIds[] = $subject->id;
                }
            }

            if (!empty($subjectIds)) {
                $user->subjects()->sync($subjectIds);
            }
        }

        return null; // Return null so ToModel doesn't double-save
    }

    /**
     * Look up subject by name or code; create it if not found.
     *
     * @param string $subjectNameOrCode
     * @return Subject|null
     */
    protected function findOrCreateSubject(string $subjectNameOrCode)
    {
        $term = trim($subjectNameOrCode);
        if (empty($term)) {
            return null;
        }

        // Try finding by code or name (case-insensitive & scoped to current admin)
        $subject = Subject::where('created_by', auth()->id())
            ->where(function ($query) use ($term) {
                $query->where('code', $term)
                      ->orWhere('name', $term);
            })
            ->first();

        if ($subject) {
            return $subject;
        }

        // Generate unique code for new subject
        $cleanedName = preg_replace('/[^A-Za-z0-9]/', '', $term);
        $baseCode = strtoupper(substr($cleanedName, 0, 6));
        if (empty($baseCode)) {
            $baseCode = 'SUBJ';
        }

        $code = $baseCode;
        $counter = 1;
        while (Subject::where('created_by', auth()->id())->where('code', $code)->exists()) {
            $suffix = (string)$counter;
            $code = strtoupper(substr($baseCode, 0, 10 - strlen($suffix)) . $suffix);
            $counter++;
        }

        return Subject::create([
            'code' => $code,
            'name' => $term,
            'created_by' => auth()->id(),
        ]);
    }
}
