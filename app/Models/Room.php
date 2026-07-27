<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'building',
        'floor',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    public function classRoutines()
    {
        return $this->hasMany(ClassRoutine::class);
    }

    public function examRoutines()
    {
        return $this->hasMany(ExamRoutine::class);
    }
}
