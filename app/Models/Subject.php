<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['code', 'name', 'created_by'];

    public function teachers()
    {
        return $this->belongsToMany(User::class);
    }
}
