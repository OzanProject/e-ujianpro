<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Multitenantable;

class ExamRoom extends Model
{
    use HasFactory, Multitenantable;

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
