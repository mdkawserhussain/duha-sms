<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'category',
        'amount',
        'academic_year',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function feeRecords()
    {
        return $this->hasMany(FeeRecord::class);
    }
}
