<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates parent categories and their subs (children).
     */
    public function run(): void
    {
        $this->seedInterview();
        $this->seedPresentation();
        $this->seedDebate();
        $this->seedStorytelling();
        $this->seedImpromptu();
    }

    private function seedInterview(): void
    {
        $parent = Category::updateOrCreate(
            ['slug' => 'interview'],
            [
                'name' => 'Interview',
                'description' => 'Practice answering common interview questions',
                'icon' => 'mic',
                'order' => 10,
            ]
        );

        $subs = [
            ['slug' => 'interview-behavioral', 'name' => 'Behavioral', 'description' => 'STAR method and situation-based questions', 'icon' => 'star', 'order' => 1],
            ['slug' => 'interview-technical', 'name' => 'Technical', 'description' => 'Technical and role-specific questions', 'icon' => 'code', 'order' => 2],
            ['slug' => 'interview-hr-general', 'name' => 'HR & General', 'description' => 'General and HR interview questions', 'icon' => 'users', 'order' => 3],
        ];

        foreach ($subs as $sub) {
            Category::updateOrCreate(
                ['slug' => $sub['slug']],
                array_merge($sub, ['parent_id' => $parent->id])
            );
        }
    }

    private function seedPresentation(): void
    {
        $parent = Category::updateOrCreate(
            ['slug' => 'presentation'],
            [
                'name' => 'Presentation',
                'description' => 'Practice explaining concepts clearly',
                'icon' => 'chart',
                'order' => 20,
            ]
        );

        $subs = [
            ['slug' => 'presentation-pitch', 'name' => 'Pitch', 'description' => 'Elevator pitches and product pitches', 'icon' => 'zap', 'order' => 1],
            ['slug' => 'presentation-explainer', 'name' => 'Explainer', 'description' => 'Explain complex ideas simply', 'icon' => 'book-open', 'order' => 2],
            ['slug' => 'presentation-training', 'name' => 'Training', 'description' => 'Training and instructional delivery', 'icon' => 'graduation-cap', 'order' => 3],
        ];

        foreach ($subs as $sub) {
            Category::updateOrCreate(
                ['slug' => $sub['slug']],
                array_merge($sub, ['parent_id' => $parent->id])
            );
        }
    }

    private function seedDebate(): void
    {
        $parent = Category::updateOrCreate(
            ['slug' => 'debate'],
            [
                'name' => 'Debate',
                'description' => 'Practice defending a position',
                'icon' => 'balance',
                'order' => 30,
            ]
        );

        $subs = [
            ['slug' => 'debate-formal', 'name' => 'Formal Debate', 'description' => 'Structured debate formats', 'icon' => 'gavel', 'order' => 1],
            ['slug' => 'debate-persuasive', 'name' => 'Persuasive Speech', 'description' => 'Persuade and influence', 'icon' => 'message-circle', 'order' => 2],
        ];

        foreach ($subs as $sub) {
            Category::updateOrCreate(
                ['slug' => $sub['slug']],
                array_merge($sub, ['parent_id' => $parent->id])
            );
        }
    }

    private function seedStorytelling(): void
    {
        $parent = Category::updateOrCreate(
            ['slug' => 'storytelling'],
            [
                'name' => 'Storytelling',
                'description' => 'Practice engaging narratives',
                'icon' => 'book',
                'order' => 40,
            ]
        );

        $subs = [
            ['slug' => 'storytelling-personal', 'name' => 'Personal Story', 'description' => 'Personal anecdotes and experiences', 'icon' => 'heart', 'order' => 1],
            ['slug' => 'storytelling-brand', 'name' => 'Brand Narrative', 'description' => 'Brand and company stories', 'icon' => 'award', 'order' => 2],
        ];

        foreach ($subs as $sub) {
            Category::updateOrCreate(
                ['slug' => $sub['slug']],
                array_merge($sub, ['parent_id' => $parent->id])
            );
        }
    }

    private function seedImpromptu(): void
    {
        $parent = Category::updateOrCreate(
            ['slug' => 'impromptu'],
            [
                'name' => 'Impromptu',
                'description' => 'Practice thinking on your feet',
                'icon' => 'lightbulb',
                'order' => 50,
            ]
        );

        $subs = [
            ['slug' => 'impromptu-quick-fire', 'name' => 'Quick Fire', 'description' => 'Short, spontaneous responses', 'icon' => 'clock', 'order' => 1],
            ['slug' => 'impromptu-table-topics', 'name' => 'Table Topics', 'description' => 'Table topics style prompts', 'icon' => 'message-square', 'order' => 2],
        ];

        foreach ($subs as $sub) {
            Category::updateOrCreate(
                ['slug' => $sub['slug']],
                array_merge($sub, ['parent_id' => $parent->id])
            );
        }
    }
}
