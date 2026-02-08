<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    title: 'Category',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'slug', type: 'string', example: 'public-speaking'),
        new OA\Property(property: 'name', type: 'string', example: 'Public Speaking'),
        new OA\Property(property: 'description', type: 'string', example: 'Practice your public speaking skills'),
        new OA\Property(property: 'icon', type: 'string', example: 'microphone'),
        new OA\Property(property: 'order', type: 'integer', example: 1),
    ],
)]
class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'icon', 'order'];

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }
}
