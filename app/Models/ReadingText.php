<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingText extends Model
{
    protected $fillable = ['subject_id', 'code', 'title', 'content'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
