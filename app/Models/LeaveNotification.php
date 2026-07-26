<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'guardian_id',
        'date',
        'reason',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
