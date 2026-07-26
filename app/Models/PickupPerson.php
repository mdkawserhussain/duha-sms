<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'relationship',
        'phone',
        'photo',
        'is_authorized',
    ];

    protected function casts(): array
    {
        return [
            'is_authorized' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeAuthorized($query)
    {
        return $query->where('is_authorized', true);
    }
}
