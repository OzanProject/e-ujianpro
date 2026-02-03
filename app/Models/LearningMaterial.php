<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'file_path',
        'file_type',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
