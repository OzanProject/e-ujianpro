<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreScale extends Model
{
    protected $fillable = [
        'institution_id',
        'question_group_id',
        'correct_count',
        'scaled_score',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function questionGroup()
    {
        return $this->belongsTo(QuestionGroup::class);
    }
}
