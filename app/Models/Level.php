<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
