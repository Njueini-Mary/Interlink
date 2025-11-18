<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Skill;
class StudentSkillSeeder extends Seeder
{
    public function run()
    {
        $students = Student::all();
        $skills = Skill::all();
        $bios = [
            'Experienced in Python, data science, and cloud computing. Interested in AI and analytics.',
            'Web developer with a passion for DevOps and Linux systems. Skilled in JavaScript and design.',
            'Aspiring AI specialist with background in machine learning and statistics. Loves solving problems.',
            'Creative designer and frontend developer. Interested in UX, marketing, and communication.',
            'Data scientist with strong skills in SQL, R, and business analytics. Enjoys teamwork and leadership.'
        ];
        $interests = [
            'AI, analytics, cloud, Python',
            'DevOps, Linux, web development',
            'Machine learning, statistics, problem solving',
            'UX, marketing, communication',
            'SQL, R, business analytics, teamwork'
        ];
        $activities = [
            'Hackathons, research, open source',
            'DevOps meetups, cloud labs',
            'Marketing campaigns, design sprints',
            'Frontend bootcamps, designathons',
            'Business case competitions, analytics clubs'
        ];
        $organizations = [
            'IEEE, Google DSC',
            'Linux Foundation',
            'Marketing Society',
            'Design Club',
            'Analytics Club'
        ];
        foreach ($students as $i => $student) {
            $randomSkills = $skills->random(min(2, $skills->count()));
            foreach ($randomSkills as $skill) {
                $student->skills()->syncWithoutDetaching([$skill->id]);
            }
            $student->bio = $bios[$i % count($bios)];
            $student->interests = $interests[$i % count($interests)];
            $student->activities = $activities[$i % count($activities)];
            $student->organizations = $organizations[$i % count($organizations)];
            $student->save();
        }
    }
}
