<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = [
        'title',
        'subject_id',
        'exam_package_id',
        'start_time',
        'end_time',
        'duration',
        'description',
        'is_active',
        'token',
        'exam_type_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function examPackage()
    {
        return $this->belongsTo(ExamPackage::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
