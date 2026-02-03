<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'nis',
        'password',
        'kelas',
        'jurusan',
        'student_group_id',
        'photo', 
        'user_id',
        'created_by' // Added
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(StudentGroup::class, 'student_group_id');
    }

    public function examRoom()
    {
        return $this->belongsTo(ExamRoom::class);
    }
}
