<?php

namespace App\Traits;

use App\Models\Subject;
use Illuminate\Support\Collection;

/**
 * Trait HasInstitutionScope
 * 
 * Helper methods untuk standardisasi akses data berdasarkan institusi.
 * Digunakan di controller untuk mempermudah filtering data.
 */
trait HasInstitutionScope
{
    /**
     * Get institution ID (admin lembaga ID) dari user yang sedang login
     * 
     * @return int
     */
    protected function getInstitutionId(): int
    {
        $user = auth()->user();
        
        // Operator menggunakan created_by (ID admin yang membuat operator)
        // Admin lembaga menggunakan ID sendiri
        // Pengajar menggunakan created_by (ID admin yang membuat pengajar)
        return in_array($user->role, ['operator', 'pengajar']) 
            ? $user->created_by 
            : $user->id;
    }

    /**
     * Get subjects yang bisa diakses oleh user
     * 
     * @return Collection
     */
    protected function getUserSubjects(): Collection
    {
        $user = auth()->user();
        
        // Pengajar hanya bisa akses subject yang di-assign
        if ($user->role === 'pengajar') {
            return $user->subjects;
        }
        
        // Admin & Operator bisa akses semua subject di institusinya
        // Trait Multitenantable sudah otomatis filter by created_by
        return Subject::all();
    }

    /**
     * Get subject IDs yang bisa diakses oleh user
     * 
     * @return array
     */
    protected function getUserSubjectIds(): array
    {
        return $this->getUserSubjects()->pluck('id')->toArray();
    }

    /**
     * Validasi apakah subject milik institusi user
     * 
     * @param int $subjectId
     * @return \App\Models\Subject
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function validateSubjectOwnership(int $subjectId): Subject
    {
        $user = auth()->user();
        
        // Untuk pengajar, cek apakah subject di-assign ke dia
        if ($user->role === 'pengajar') {
            $subject = $user->subjects()->findOrFail($subjectId);
        } else {
            // Untuk admin/operator, trait Multitenantable sudah filter
            $subject = Subject::findOrFail($subjectId);
        }
        
        return $subject;
    }

    /**
     * Check apakah user punya akses ke subject tertentu
     * 
     * @param int $subjectId
     * @return bool
     */
    protected function hasSubjectAccess(int $subjectId): bool
    {
        try {
            $this->validateSubjectOwnership($subjectId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get base route name berdasarkan role user
     * Untuk controller yang punya route berbeda per role
     * 
     * @param string $prefix (e.g., 'exam_session', 'question')
     * @return string
     */
    protected function getBaseRoute(string $prefix): string
    {
        $user = auth()->user();
        
        return $user->role === 'pengajar' 
            ? "pengajar.{$prefix}" 
            : "admin.{$prefix}";
    }

    /**
     * Abort jika user tidak punya akses ke subject
     * 
     * @param int $subjectId
     * @param string $message
     * @return void
     */
    protected function abortIfNoSubjectAccess(int $subjectId, string $message = 'Akses Ditolak.'): void
    {
        if (!$this->hasSubjectAccess($subjectId)) {
            abort(403, $message);
        }
    }
}
