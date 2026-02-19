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
        new OA\Property(property: 'parent_id', type: 'integer', nullable: true, example: null),
        new OA\Property(property: 'slug', type: 'string', example: 'public-speaking'),
        new OA\Property(property: 'name', type: 'string', example: 'Public Speaking'),
        new OA\Property(property: 'description', type: 'string', example: 'Practice your public speaking skills'),
        new OA\Property(property: 'icon', type: 'string', example: 'microphone'),
        new OA\Property(property: 'order', type: 'integer', example: 1),
        new OA\Property(property: 'subs', type: 'array', items: new OA\Items(ref: '#/components/schemas/Category'), description: 'Child categories (only on parent categories)'),
    ],
)]
class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = ['parent_id', 'slug', 'name', 'description', 'icon', 'order'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }
}
