<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'slug' => 'interview',
            'name' => 'Interview',
            'description' => 'Practice answering common interview questions',
            'icon' => 'mic',
            'order' => 10,
        ]);

        Category::create([
            'slug' => 'presentation',
            'name' => 'Presentation',
            'description' => 'Practice explaining concepts clearly',
            'icon' => 'chart',
            'order' => 20,
        ]);

        Category::create([
            'slug' => 'debate',
            'name' => 'Debate',
            'description' => 'Practice defending a position',
            'icon' => 'balance',
            'order' => 30,
        ]);

        Category::create([
            'slug' => 'storytelling',
            'name' => 'Storytelling',
            'description' => 'Practice engaging narratives',
            'icon' => 'book',
            'order' => 40,
        ]);

        Category::create([
            'slug' => 'impromptu',
            'name' => 'Impromptu',
            'description' => 'Practice thinking on your feet',
            'icon' => 'lightbulb',
            'order' => 50,
        ]);
    }
}
