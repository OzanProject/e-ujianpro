<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Multitenantable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, Multitenantable;

    protected $fillable = [
        'name',
        'gender',
        'email',
        'phone_number',
        'nis',
        'nisn',
        'password',
        'password_text',
        'kelas',
        'jurusan',
        'participant_number',
        'student_group_id',
        'exam_room_id',
        'photo',
        'user_id',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Relasi ke user/admin/pemilik data.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke rombel / kelompok siswa.
     */
    public function group()
    {
        return $this->belongsTo(StudentGroup::class, 'student_group_id');
    }

    /**
     * Relasi ke ruang ujian.
     *
     * Pastikan tabel students punya kolom exam_room_id.
     */
    public function examRoom()
    {
        return $this->belongsTo(ExamRoom::class, 'exam_room_id');
    }

    /**
     * Relasi ke percobaan ujian siswa.
     */
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    /**
     * Relasi ke jawaban siswa melalui exam attempt.
     */
    public function examAnswers()
    {
        return $this->hasManyThrough(
            ExamAnswer::class,
            ExamAttempt::class,
            'student_id',
            'exam_attempt_id',
            'id',
            'id'
        );
    }

    /**
     * Attempt ujian yang sedang berlangsung.
     */
    public function activeExamAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id')
            ->where('status', 'in_progress');
    }

    /**
     * Attempt ujian yang sudah selesai.
     */
    public function completedExamAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id')
            ->where('status', 'completed');
    }

    /**
     * Accessor nama kelas/kelompok yang aman dipakai di view.
     */
    public function getClassLabelAttribute()
    {
        if ($this->group) {
            return $this->group->name;
        }

        return $this->kelas ?? '-';
    }

    /**
     * Accessor foto siswa.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        return asset('img/default-user.png');
    }
}