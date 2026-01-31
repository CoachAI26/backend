<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    /** @use HasFactory<\Database\Factories\ChallengeFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id', 'level_id', 'title', 'suggested_time_minutes', 'hints_available', 'tips',
    ];

    protected $casts = [
        'tips' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function practiceSessions()
    {
        return $this->hasMany(PracticeSession::class);
    }
}
