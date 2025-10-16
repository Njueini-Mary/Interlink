<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Skill;
class SkillSeeder extends Seeder
{
    public function run()
    {
        $skills = ['Devops', 'Linux', 'Python', 'AI', 'Web Development', 'Data Science'];
        foreach ($skills as $skill) {
            Skill::firstOrCreate(['name' => $skill]);
        }
    }
}
