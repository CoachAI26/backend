<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Level::create([
            'slug' => 'easy',
            'name' => 'Easy',
            'description' => 'Basic warm-up questions',
            'color' => '#4CAF50',
            'min_score' => 0,
            'order' => 10,
        ]);

        Level::create([
            'slug' => 'medium',
            'name' => 'Medium',
            'description' => 'Moderate difficulty',
            'color' => '#FF9800',
            'min_score' => 40,
            'order' => 20,
        ]);

        Level::create([
            'slug' => 'hard',
            'name' => 'Hard',
            'description' => 'Advanced / stressful',
            'color' => '#F44336',
            'min_score' => 70,
            'order' => 30,
        ]);
    }
}
