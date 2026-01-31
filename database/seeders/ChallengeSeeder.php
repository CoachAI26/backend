<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Level;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interview = Category::where('slug', 'interview')->first();
        $presentation = Category::where('slug', 'presentation')->first();

        $easy = Level::where('slug', 'easy')->first();
        $medium = Level::where('slug', 'medium')->first();

        Challenge::create([
            'category_id' => $interview->id,
            'level_id' => $easy->id,
            'title' => 'Tell me about yourself and your professional background.',
            'suggested_time_minutes' => 2,
            'hints_available' => 3,
            'tips' => null,
        ]);

        Challenge::create([
            'category_id' => $interview->id,
            'level_id' => $easy->id,
            'title' => 'What is your greatest strength and how has it helped you professionally?',
            'suggested_time_minutes' => 2,
            'hints_available' => 3,
            'tips' => null,
        ]);

        Challenge::create([
            'category_id' => $interview->id,
            'level_id' => $medium->id,
            'title' => 'Describe a challenging situation at work and how you handled it.',
            'suggested_time_minutes' => 3,
            'hints_available' => 3,
            'tips' => null,
        ]);

        Challenge::create([
            'category_id' => $interview->id,
            'level_id' => $medium->id,
            'title' => 'Where do you see yourself in five years?',
            'suggested_time_minutes' => 2,
            'hints_available' => 3,
            'tips' => null,
        ]);

        Challenge::create([
            'category_id' => $presentation->id,
            'level_id' => $medium->id,
            'title' => 'Explain a complex concept from your field to someone with no background in it.',
            'suggested_time_minutes' => 3,
            'hints_available' => 0,
            'tips' => ['Use analogies', 'Avoid jargon', 'Check for understanding'],
        ]);
    }
}
