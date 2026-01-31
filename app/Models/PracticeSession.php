<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PracticeSession extends Model
{
    /** @use HasFactory<\Database\Factories\PracticeSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'name',               // user-provided or auto-generated
        'started_at',
        'completed_at',
        'status',             // 'started', 'recorded', 'processed', 'failed'
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(PracticeResult::class);
    }

    // Helper: auto-name if not provided
    protected static function booted()
    {
        static::creating(function (self $session) {
            if (empty($session->name)) {
                $challenge = $session->challenge;
                $session->name = $challenge ? sprintf(
                    "%s Practice - %s",
                    $challenge->title,
                    now()->format('M d, Y h:i A')
                ) : 'Untitled Practice';
            }
        });
    }
}
