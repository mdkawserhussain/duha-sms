<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'body',
        'status',
        'reviewed_by',
        // Admission fields
        'child_name',
        'child_dob',
        'child_gender',
        'previous_school',
        'documents',
        'photo',
        'class_id',
        'guardian_info',
        'additional_notes',
    ];

    protected function casts(): array
    {
        return [
            'child_dob' => 'date',
            'documents' => 'array',
            'guardian_info' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAdmissions($query)
    {
        return $query->where('type', 'admission');
    }
}
