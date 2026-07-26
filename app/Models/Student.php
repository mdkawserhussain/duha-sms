<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guardian_id',
        'name',
        'dob',
        'gender',
        'class_id',
        'admission_date',
        'admission_no',
        'blood_group',
        'photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'admission_date' => 'date',
        ];
    }

    // Relationships
    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function diaryEntries()
    {
        return $this->hasMany(DiaryEntry::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    public function workSamples()
    {
        return $this->hasMany(WorkSample::class);
    }

    public function feeRecords()
    {
        return $this->hasMany(FeeRecord::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function pickupPersons()
    {
        return $this->hasMany(PickupPerson::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function leaveNotifications()
    {
        return $this->hasMany(LeaveNotification::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForGuardian($query, $guardianId)
    {
        return $query->where('guardian_id', $guardianId);
    }
}
