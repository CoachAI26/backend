<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Challenge',
    title: 'Challenge',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Introduce yourself to a new team'),
        new OA\Property(property: 'suggested_time_minutes', type: 'integer', example: 5),
        new OA\Property(property: 'hints_available', type: 'boolean', example: true),
        new OA\Property(property: 'tips', type: 'array', items: new OA\Items(type: 'string'), example: ['Speak clearly', 'Make eye contact']),
        new OA\Property(property: 'category', ref: '#/components/schemas/Category', nullable: true),
        new OA\Property(property: 'level', ref: '#/components/schemas/Level', nullable: true),
    ],
)]
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
