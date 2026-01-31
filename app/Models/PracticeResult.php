<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeResult extends Model
{
    /** @use HasFactory<\Database\Factories\PracticeResultFactory> */
    use HasFactory;

    protected $fillable = [
        'practice_session_id',
        'transcription',
        'feedback',
        'score',              // e.g. 7.5/10 parsed from AI or calculated
        'metadata',           // json: fillers_count, pace_wpm, etc.
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(PracticeSession::class, 'practice_session_id');
    }
}
