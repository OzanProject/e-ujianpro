<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['subject_id', 'reading_text_id', 'question_group_id', 'content', 'type'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function readingText()
    {
        return $this->belongsTo(ReadingText::class);
    }

    public function questionGroup()
    {
        return $this->belongsTo(QuestionGroup::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }
}
