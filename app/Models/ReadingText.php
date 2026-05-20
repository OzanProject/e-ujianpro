<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class ReadingText extends Model
{
    use Multitenantable;

    protected $fillable = ['subject_id', 'code', 'title', 'content', 'created_by'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
