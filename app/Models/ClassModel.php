<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'section',
        'capacity',
        'academic_year',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'class_teacher', 'class_id', 'teacher_id')
            ->withPivot('is_primary');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function diaryEntries()
    {
        return $this->hasMany(DiaryEntry::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function routines()
    {
        return $this->hasMany(ClassRoutine::class);
    }

    public function examRoutines()
    {
        return $this->hasMany(ExamRoutine::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    // Helper methods
    public function getStudentCountAttribute(): int
    {
        return $this->students()->active()->count();
    }

    public function getCapacityPercentageAttribute(): float
    {
        if ($this->capacity === 0) return 0;
        return round(($this->student_count / $this->capacity) * 100, 1);
    }
}
