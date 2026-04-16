<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Multitenantable;

class ExamPackage extends Model
{
    use HasFactory, Multitenantable;

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
