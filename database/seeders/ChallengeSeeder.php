<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Level;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates challenges for sub-categories (and keeps some on parent-related subs).
     */
    public function run(): void
    {
        $easy = Level::where('slug', 'easy')->first();
        $medium = Level::where('slug', 'medium')->first();
        $hard = Level::where('slug', 'hard')->first();

        $this->seedInterviewChallenges($easy, $medium, $hard);
        $this->seedPresentationChallenges($easy, $medium, $hard);
        $this->seedDebateChallenges($easy, $medium, $hard);
        $this->seedStorytellingChallenges($easy, $medium, $hard);
        $this->seedImpromptuChallenges($easy, $medium, $hard);
    }

    private function seedInterviewChallenges($easy, $medium, $hard): void
    {
        $behavioral = Category::where('slug', 'interview-behavioral')->first();
        $technical = Category::where('slug', 'interview-technical')->first();
        $hrGeneral = Category::where('slug', 'interview-hr-general')->first();

        $challenges = [
            // Behavioral
            ['category' => $behavioral, 'level' => $easy, 'title' => 'Describe a time when you had to work with a difficult team member. What was the outcome?', 'mins' => 2, 'hints' => 3],
            ['category' => $behavioral, 'level' => $easy, 'title' => 'Tell me about a goal you set and how you achieved it.', 'mins' => 2, 'hints' => 3],
            ['category' => $behavioral, 'level' => $medium, 'title' => 'Describe a challenging situation at work and how you handled it.', 'mins' => 3, 'hints' => 3],
            ['category' => $behavioral, 'level' => $medium, 'title' => 'Give an example of when you failed and what you learned from it.', 'mins' => 3, 'hints' => 3],
            ['category' => $behavioral, 'level' => $hard, 'title' => 'Describe a time you had to make an unpopular decision. How did you communicate it?', 'mins' => 3, 'hints' => 2],
            // Technical
            ['category' => $technical, 'level' => $medium, 'title' => 'Walk me through your approach to debugging a complex production issue.', 'mins' => 4, 'hints' => 2, 'tips' => ['Structure your answer', 'Mention tools and logs']],
            ['category' => $technical, 'level' => $medium, 'title' => 'Explain a technical concept from your field to a non-technical stakeholder.', 'mins' => 3, 'hints' => 2],
            ['category' => $technical, 'level' => $hard, 'title' => 'Describe the most challenging technical problem you solved recently.', 'mins' => 4, 'hints' => 2],
            // HR & General
            ['category' => $hrGeneral, 'level' => $easy, 'title' => 'Tell me about yourself and your professional background.', 'mins' => 2, 'hints' => 3],
            ['category' => $hrGeneral, 'level' => $easy, 'title' => 'What is your greatest strength and how has it helped you professionally?', 'mins' => 2, 'hints' => 3],
            ['category' => $hrGeneral, 'level' => $medium, 'title' => 'Where do you see yourself in five years?', 'mins' => 2, 'hints' => 3],
            ['category' => $hrGeneral, 'level' => $medium, 'title' => 'Why do you want to leave your current role?', 'mins' => 2, 'hints' => 2],
            ['category' => $hrGeneral, 'level' => $hard, 'title' => 'What is your biggest weakness and how are you working on it?', 'mins' => 2, 'hints' => 2],
        ];

        foreach ($challenges as $c) {
            $this->createChallenge($c);
        }
    }

    private function seedPresentationChallenges($easy, $medium, $hard): void
    {
        $pitch = Category::where('slug', 'presentation-pitch')->first();
        $explainer = Category::where('slug', 'presentation-explainer')->first();
        $training = Category::where('slug', 'presentation-training')->first();

        $challenges = [
            // Pitch
            ['category' => $pitch, 'level' => $easy, 'title' => 'Give a 60-second elevator pitch for a product you use daily.', 'mins' => 1, 'hints' => 3],
            ['category' => $pitch, 'level' => $medium, 'title' => 'Pitch an idea to improve your workplace to a skeptical audience.', 'mins' => 2, 'hints' => 2],
            ['category' => $pitch, 'level' => $hard, 'title' => 'Pitch a new product as if to investors in under three minutes.', 'mins' => 3, 'hints' => 2],
            // Explainer
            ['category' => $explainer, 'level' => $medium, 'title' => 'Explain a complex concept from your field to someone with no background in it.', 'mins' => 3, 'hints' => 0, 'tips' => ['Use analogies', 'Avoid jargon', 'Check for understanding']],
            ['category' => $explainer, 'level' => $easy, 'title' => 'Explain how the internet works in two minutes.', 'mins' => 2, 'hints' => 3],
            ['category' => $explainer, 'level' => $hard, 'title' => 'Explain machine learning to a group of high school students.', 'mins' => 4, 'hints' => 2],
            // Training
            ['category' => $training, 'level' => $medium, 'title' => 'Teach a colleague how to use a tool or process you know well.', 'mins' => 3, 'hints' => 2],
            ['category' => $training, 'level' => $easy, 'title' => 'Give a short safety or onboarding briefing for new team members.', 'mins' => 2, 'hints' => 3],
        ];
        foreach ($challenges as $c) {
            $this->createChallenge($c);
        }
    }

    private function seedDebateChallenges($easy, $medium, $hard): void
    {
        $formal = Category::where('slug', 'debate-formal')->first();
        $persuasive = Category::where('slug', 'debate-persuasive')->first();

        $challenges = [
            ['category' => $formal, 'level' => $medium, 'title' => 'Argue for: Remote work should be the default for knowledge workers.', 'mins' => 3, 'hints' => 2],
            ['category' => $formal, 'level' => $medium, 'title' => 'Argue against: AI will do more harm than good in the next decade.', 'mins' => 3, 'hints' => 2],
            ['category' => $formal, 'level' => $hard, 'title' => 'Present both sides of the debate on universal basic income.', 'mins' => 4, 'hints' => 2],
            ['category' => $persuasive, 'level' => $easy, 'title' => 'Convince someone to try a hobby you enjoy.', 'mins' => 2, 'hints' => 3],
            ['category' => $persuasive, 'level' => $medium, 'title' => 'Persuade your manager to approve a training budget increase.', 'mins' => 3, 'hints' => 2],
            ['category' => $persuasive, 'level' => $hard, 'title' => 'Make a case for changing an existing company policy.', 'mins' => 3, 'hints' => 2],
        ];
        foreach ($challenges as $c) {
            $this->createChallenge($c);
        }
    }

    private function seedStorytellingChallenges($easy, $medium, $hard): void
    {
        $personal = Category::where('slug', 'storytelling-personal')->first();
        $brand = Category::where('slug', 'storytelling-brand')->first();

        $challenges = [
            ['category' => $personal, 'level' => $easy, 'title' => 'Share a short story about a lesson you learned from a mistake.', 'mins' => 2, 'hints' => 3],
            ['category' => $personal, 'level' => $medium, 'title' => 'Tell the story of a time you overcame a fear or obstacle.', 'mins' => 3, 'hints' => 2],
            ['category' => $personal, 'level' => $hard, 'title' => 'Narrate a turning point in your career or life in under three minutes.', 'mins' => 3, 'hints' => 2],
            ['category' => $brand, 'level' => $medium, 'title' => 'Tell the story of how your company or product started.', 'mins' => 3, 'hints' => 2],
            ['category' => $brand, 'level' => $hard, 'title' => 'Present a customer success story that highlights your product impact.', 'mins' => 4, 'hints' => 2],
        ];
        foreach ($challenges as $c) {
            $this->createChallenge($c);
        }
    }

    private function seedImpromptuChallenges($easy, $medium, $hard): void
    {
        $quickFire = Category::where('slug', 'impromptu-quick-fire')->first();
        $tableTopics = Category::where('slug', 'impromptu-table-topics')->first();

        $challenges = [
            ['category' => $quickFire, 'level' => $easy, 'title' => 'If you could have dinner with anyone, living or dead, who would it be and why?', 'mins' => 1, 'hints' => 3],
            ['category' => $quickFire, 'level' => $easy, 'title' => 'What is one thing you would change about your morning routine?', 'mins' => 1, 'hints' => 3],
            ['category' => $quickFire, 'level' => $medium, 'title' => 'You have 60 seconds to defend the importance of reading books.', 'mins' => 1, 'hints' => 2],
            ['category' => $quickFire, 'level' => $hard, 'title' => 'Give an impromptu one-minute talk on a random word drawn from a hat.', 'mins' => 1, 'hints' => 1],
            ['category' => $tableTopics, 'level' => $medium, 'title' => 'Table topic: What does success mean to you?', 'mins' => 2, 'hints' => 2],
            ['category' => $tableTopics, 'level' => $medium, 'title' => 'Table topic: Describe your ideal day from wake-up to sleep.', 'mins' => 2, 'hints' => 2],
            ['category' => $tableTopics, 'level' => $hard, 'title' => 'Table topic: If you could live in any era, which would you choose and why?', 'mins' => 2, 'hints' => 2],
        ];
        foreach ($challenges as $c) {
            $this->createChallenge($c);
        }
    }

    private function createChallenge(array $c): void
    {
        Challenge::updateOrCreate(
            [
                'category_id' => $c['category']->id,
                'title' => $c['title'],
            ],
            [
                'level_id' => $c['level']->id,
                'suggested_time_minutes' => $c['mins'],
                'hints_available' => $c['hints'] ?? 0,
                'tips' => $c['tips'] ?? null,
            ]
        );
    }
}
