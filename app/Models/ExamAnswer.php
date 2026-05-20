<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use Multitenantable;

    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'question_option_id',
        'answer_text',
        'score',
        'is_correct',
        'is_doubtful',
        'created_by', // Added for tenant isolation
    ];

    protected $casts = [
        'score' => 'float',
        'is_correct' => 'boolean',
        'is_doubtful' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
