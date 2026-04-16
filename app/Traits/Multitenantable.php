<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Multitenantable
{
    /**
     * Boot the trait to add a global scope.
     */
    protected static function bootMultitenantable(): void
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Jika Super Admin, biarkan melihat semua data (mode pantau)
            if ($user->role === 'super_admin') {
                return;
            }

            // Tentukan ID Institusi (Admin Utama)
            $institutionId = ($user->role === 'admin_lembaga') ? $user->id : $user->created_by;

            static::creating(function ($model) use ($institutionId) {
                // Otomatis set created_by saat membuat data baru
                if (!$model->created_by) {
                    $model->created_by = $institutionId;
                }
            });

            static::addGlobalScope('created_by', function (Builder $builder) use ($institutionId) {
                $builder->where('created_by', $institutionId);
            });
        }
    }
}
