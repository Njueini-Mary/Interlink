<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Opportunity;
use App\Models\Skill;
class OpportunitySkillSeeder extends Seeder
{
    public function run()
    {
        $opportunities = Opportunity::all();
        $skills = Skill::all();
        $descriptions = [
            'Work on cutting-edge AI and analytics projects. Collaborate with data scientists and engineers.',
            'Join our DevOps and web development team. Focus on cloud infrastructure and Linux systems.',
            'Research machine learning and statistics for real-world applications. Solve challenging problems.',
            'Design and develop creative marketing campaigns. Improve UX and communication strategies.',
            'Analyze business data using SQL, R, and analytics tools. Lead projects and work in teams.'
        ];
        foreach ($opportunities as $i => $opportunity) {
            $randomSkills = $skills->random(min(2, $skills->count()));
            foreach ($randomSkills as $skill) {
                $opportunity->skills()->syncWithoutDetaching([$skill->id]);
            }
            $opportunity->description = $descriptions[$i % count($descriptions)];
            $opportunity->save();
        }
    }
}
