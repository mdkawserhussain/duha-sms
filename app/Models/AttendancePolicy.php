<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_data',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'policy_data' => 'array',
        ];
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPolicy(): array
    {
        return $this->policy_data ?? [];
    }
}
