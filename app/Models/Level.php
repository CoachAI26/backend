<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Level',
    title: 'Level',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'slug', type: 'string', example: 'beginner'),
        new OA\Property(property: 'name', type: 'string', example: 'Beginner'),
        new OA\Property(property: 'description', type: 'string', example: 'For those just starting out'),
        new OA\Property(property: 'color', type: 'string', example: '#4CAF50'),
        new OA\Property(property: 'min_score', type: 'number', format: 'float', example: 0),
        new OA\Property(property: 'order', type: 'integer', example: 1),
    ],
)]
class Level extends Model
{
    /** @use HasFactory<\Database\Factories\LevelFactory> */
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'color', 'min_score', 'order'];

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }
}
