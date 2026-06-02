<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use Multitenantable;

    protected $fillable = [
        'exam_session_id',
        'student_id',
        'start_time',
        'end_time',
        'score',
        'status',
        'cheat_count',
        'created_by', // Added for tenant isolation
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'score' => 'float',
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attempt) {
            // Delete related answers
            $attempt->answers()->delete();
        });
    }
}
