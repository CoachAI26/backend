<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PracticeSession',
    title: 'PracticeSession',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Introduce yourself Practice - Feb 08, 2026 10:30 AM'),
        new OA\Property(property: 'challenge', ref: '#/components/schemas/Challenge', nullable: true),
        new OA\Property(property: 'started_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'status', type: 'string', enum: ['started', 'recorded', 'processed', 'failed'], example: 'processed'),
        new OA\Property(property: 'result', type: 'object', nullable: true, description: 'Included when the result relationship is loaded', properties: [
            new OA\Property(property: 'transcription', type: 'string'),
            new OA\Property(property: 'feedback', type: 'string'),
            new OA\Property(property: 'score', type: 'number', format: 'float'),
            new OA\Property(property: 'metadata', type: 'object'),
        ]),
    ],
)]
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
