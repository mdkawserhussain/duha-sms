<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaryComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'diary_entry_id',
        'user_id',
        'comment',
    ];

    public function diaryEntry()
    {
        return $this->belongsTo(DiaryEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
