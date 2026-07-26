<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'amount',
        'status',
        'paid_date',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'paid_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'due');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }
}
