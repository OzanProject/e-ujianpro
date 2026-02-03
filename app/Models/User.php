<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'max_students',
        'points_balance',
        'whatsapp',
        'photo',
        'created_by',
        'exam_room_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // Accessor for all students created by this user (Admin Lembaga)
    public function students()
    {
        // Admin -> created_by -> Student (Profile)
        // Since we added 'created_by' directly to 'students' table, we can use hasMany directly
        return $this->hasMany(Student::class, 'created_by');
    }

    public function institution()
    {
        return $this->hasOne(Institution::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function examRoom()
    {
        return $this->belongsTo(ExamRoom::class);
    }

    /**
     * Check if user can add more students based on quota.
     * 
     * @param int $count Number of students to add
     * @return bool
     */
    public function canAddStudents(int $count = 1): bool
    {
        // If max_students is null, it means unlimited
        if (is_null($this->max_students)) {
            return true;
        }

        $currentCount = $this->students()->count();

        return ($currentCount + $count) <= $this->max_students;
    }
}
