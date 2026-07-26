<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'class_id',
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function examRoutines()
    {
        return $this->hasMany(ExamRoutine::class);
    }

    public function routines()
    {
        return $this->hasMany(ClassRoutine::class);
    }
}
