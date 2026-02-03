<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPackage extends Model
{
    protected $fillable = ['subject_id', 'name', 'code', 'created_by'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_package_question');
    }
}
