<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'status',
        'dob',
        'address',
        'is_first_login',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'is_first_login' => 'boolean',
        ];
    }

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }

    public function assignedClasses()
    {
        return $this->belongsToMany(ClassModel::class, 'class_teacher', 'teacher_id', 'class_id')
            ->withPivot('is_primary');
    }

    public function teacherAttendances()
    {
        return $this->hasMany(TeacherAttendance::class, 'teacher_id');
    }

    public function diaryEntries()
    {
        return $this->hasMany(DiaryEntry::class, 'teacher_id');
    }

    public function diaryComments()
    {
        return $this->hasMany(DiaryComment::class, 'user_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluated_by');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function profileChangeRequests()
    {
        return $this->hasMany(ProfileChangeRequest::class);
    }

    public function leaveNotifications()
    {
        return $this->hasMany(LeaveNotification::class, 'guardian_id');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isGuardian(): bool
    {
        return $this->role === 'guardian';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function needsPasswordChange(): bool
    {
        return $this->is_first_login;
    }
}
