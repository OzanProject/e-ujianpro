<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamRoom extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function proctors()
    {
        return $this->hasMany(User::class)->where('role', 'proctor');
    }
}
