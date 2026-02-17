<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PracticeResult',
    title: 'PracticeResult',
    description: 'AI-generated analysis of a speech recording',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'practice_session_id', type: 'integer', example: 1),
        new OA\Property(property: 'transcription', type: 'string', example: 'Hello everyone, my name is John and I am excited to be here today.'),
        new OA\Property(property: 'feedback', type: 'string', example: "Overall Rating: Good\nConfidence Score: 78/100\nFluency Score: 82/100"),
        new OA\Property(property: 'improved_text', type: 'string', nullable: true, example: 'Hello everyone, my name is John, and I am excited to be here today.', description: 'AI-improved version of the transcribed speech'),
        new OA\Property(property: 'score', type: 'number', format: 'float', example: 78.50, description: 'Confidence score 0-100'),
        new OA\Property(property: 'metadata', type: 'object', description: 'Detailed speech analysis data', properties: [
            new OA\Property(property: 'improved_text', type: 'string', nullable: true),
            new OA\Property(property: 'tts_speech', type: 'object', nullable: true, properties: [
                new OA\Property(property: 'audio_content', type: 'string'),
                new OA\Property(property: 'audio_format', type: 'string'),
                new OA\Property(property: 'voice', type: 'string'),
            ]),
            new OA\Property(property: 'cleaned_text', type: 'string'),
            new OA\Property(property: 'filler_words', type: 'array', items: new OA\Items(type: 'object')),
            new OA\Property(property: 'filler_count', type: 'integer', example: 3),
            new OA\Property(property: 'duration_seconds', type: 'number', format: 'float', example: 45.2),
            new OA\Property(property: 'word_count', type: 'integer', example: 98),
            new OA\Property(property: 'wpm', type: 'number', format: 'float', example: 130),
            new OA\Property(property: 'total_pauses', type: 'integer', example: 5),
            new OA\Property(property: 'total_hesitations', type: 'integer', example: 2),
            new OA\Property(property: 'pause_durations', type: 'array', items: new OA\Items(type: 'number')),
            new OA\Property(property: 'average_pause_duration', type: 'number', format: 'float', example: 0.8),
            new OA\Property(property: 'total_pause_time', type: 'number', format: 'float', example: 4.0),
            new OA\Property(property: 'hesitation_words', type: 'array', items: new OA\Items(type: 'string')),
            new OA\Property(property: 'fluency_score', type: 'number', format: 'float', example: 82),
            new OA\Property(property: 'pause_ratio', type: 'number', format: 'float', example: 0.09),
            new OA\Property(property: 'hesitation_rate', type: 'number', format: 'float', example: 2.04),
            new OA\Property(property: 'wpm_score', type: 'number', format: 'float', example: 85),
            new OA\Property(property: 'filler_score', type: 'number', format: 'float', example: 70),
            new OA\Property(property: 'pause_score', type: 'number', format: 'float', example: 75),
            new OA\Property(property: 'hesitation_score', type: 'number', format: 'float', example: 80),
            new OA\Property(property: 'overall_rating', type: 'string', example: 'Good', enum: ['Excellent', 'Good', 'Moderate', 'Low', 'Very Low']),
            new OA\Property(property: 'recommendations', type: 'array', items: new OA\Items(type: 'string')),
        ]),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
)]
class PracticeResult extends Model
{
    /** @use HasFactory<\Database\Factories\PracticeResultFactory> */
    use HasFactory;

    protected $fillable = [
        'practice_session_id',
        'transcription',
        'feedback',
        'improved_text',
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
