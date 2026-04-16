<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'reading_text_id',
        'question_group_id',
        'content',
        'type',
        'difficulty',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

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
