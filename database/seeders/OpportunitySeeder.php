<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Opportunity;
use App\Models\Organization;
use App\Models\Category;
use App\Models\User;

class OpportunitySeeder extends Seeder
{
    public function run()
    {
        $orgs = Organization::all();
        $categories = Category::all();
        $users = User::where('role', 'organization')->get();
        if ($categories->isEmpty()) {
            // Add a few default categories if none exist
            $catNames = ['Technology', 'Business', 'Marketing', 'Design', 'Analytics'];
            foreach ($catNames as $cat) {
                $categories->push(\App\Models\Category::create(['name' => $cat]));
            }
        }
        if ($orgs->isEmpty() || $categories->isEmpty() || $users->isEmpty()) {
            echo "[OpportunitySeeder] Skipped: Not enough organizations, categories, or users.\n";
            return;
        }
        // --- Add a "perfect match" opportunity ---
        $perfectOpp = Opportunity::create([
            'title' => 'AI & Data Science Internship',
            'organization_id' => $orgs->first()->id,
            'category_id' => $categories->first()->id,
            'description' => 'Internship for a top Computer Science student with strong AI, Data Science, and Python skills. Hosted by Data Science Society.',
            'industry' => 'Technology',
            'min_gpa' => 3.8,
            'interests' => 'AI, Data Science, Python',
            'activities' => 'Hackathons, research, open source',
            'organizations' => 'Data Science Society',
            'course' => 'Computer Science',
            'academic_field' => 'Data Science',
            'year' => 4,
            'university' => 'University of Nairobi',
            'type' => 'WBL',
        ]);
        $perfectSkills = ['AI', 'Data Science', 'Python', 'Linux'];
        foreach ($perfectSkills as $skill) {
            $skillModel = \App\Models\Skill::firstOrCreate(['name' => $skill]);
            $perfectOpp->skills()->attach($skillModel->id);
        }

        // Add a "Service Based Learning" (SBL) high-match opportunity
        $sblOpp = Opportunity::create([
            'title' => 'SBL Data Science Project',
            'organization_id' => $orgs->get(1)->id ?? $orgs->first()->id,
            'category_id' => $categories->first()->id,
            'description' => 'Service-based learning project for a student with strong AI, Data Science, and Python skills. Hosted by Nairobi Tech Hub.',
            'industry' => 'Technology',
            'min_gpa' => 3.8,
            'interests' => 'AI, Data Science, Python',
            'activities' => 'Hackathons, research, open source',
            'organizations' => 'Nairobi Tech Hub',
            'course' => 'Computer Science',
            'academic_field' => 'Data Science',
            'year' => 4,
            'university' => 'University of Nairobi',
            'type' => 'SBL',
        ]);
        foreach ($perfectSkills as $skill) {
            $skillModel = \App\Models\Skill::firstOrCreate(['name' => $skill]);
            $sblOpp->skills()->attach($skillModel->id);
        }
        // Add 10 more realistic opportunities
        $oppTitles = [
            'Frontend Developer Internship',
            'Business Data Analyst',
            'Healthcare Analytics Intern',
            'AgriTech Software Engineer',
            'Logistics Optimization Intern',
            'UX/UI Design Trainee',
            'Robotics Research Assistant',
            'Education Technology Fellow',
            'Green Energy Analyst',
            'Fintech Product Intern'
        ];
        $oppDescriptions = [
            'Work on modern web apps using React and Vue.',
            'Analyze business data for actionable insights.',
            'Support healthcare data projects and dashboards.',
            'Develop software for smart farming solutions.',
            'Help optimize supply chain and logistics.',
            'Assist in designing user interfaces and experiences.',
            'Join robotics R&D for automation projects.',
            'Contribute to digital learning platforms.',
            'Analyze renewable energy data and trends.',
            'Work on fintech products and mobile apps.'
        ];
        $oppIndustries = [
            'Technology', 'Business', 'Healthcare', 'Agriculture', 'Logistics', 'Design', 'Robotics', 'Education', 'Energy', 'Finance'
        ];
        $oppOrganizations = [
            'Creative Design Studio',
            'Smart Logistics Ltd',
            'Kenya Health Innovators',
            'AgriTech Africa',
            'Smart Logistics Ltd',
            'Creative Design Studio',
            'East Africa Robotics',
            'EduTech Kenya',
            'Green Energy Solutions',
            'Mombasa Fintech Group'
        ];
        $oppCourses = [
            'Software Engineering', 'Business Analytics', 'Health Informatics', 'Computer Engineering', 'Logistics Management', 'Design', 'Mechatronics', 'Education Technology', 'Renewable Energy', 'Finance'
        ];
        $oppFields = [
            'Web Development', 'Data Analytics', 'Healthcare Analytics', 'Software Engineering', 'Supply Chain', 'UX/UI Design', 'Robotics', 'EdTech', 'Energy Analytics', 'Fintech'
        ];
        $oppUniversities = [
            'Strathmore University', 'Kenyatta University', 'Moi University', 'Jomo Kenyatta University', 'Technical University of Kenya', 'University of Nairobi', 'Dedan Kimathi University', 'Maseno University', 'Egerton University', 'Pwani University'
        ];
        for ($i = 0; $i < 10; $i++) {
            $orgIdx = $i % count($orgs);
            $opp = Opportunity::create([
                'title' => $oppTitles[$i],
                'organization_id' => $orgs[$orgIdx]->id,
                'category_id' => $categories->first()->id,
                'description' => $oppDescriptions[$i],
                'industry' => $oppIndustries[$i],
                'min_gpa' => 3.0 + ($i % 2) * 0.5,
                'interests' => 'AI, Data Science, Python',
                'activities' => 'Hackathons, research, open source',
                'organizations' => $oppOrganizations[$i],
                'course' => $oppCourses[$i],
                'academic_field' => $oppFields[$i],
                'year' => 3 + ($i % 2),
                'university' => $oppUniversities[$i],
                'type' => $i % 2 === 0 ? 'WBL' : 'SBL',
            ]);
            $skills = ['AI', 'Data Science', 'Python', 'Linux', 'React', 'SQL', 'R', 'Figma', 'Robotics', 'Cloud'];
            shuffle($skills);
            foreach (array_slice($skills, 0, 4) as $skill) {
                $skillModel = \App\Models\Skill::firstOrCreate(['name' => $skill]);
                $opp->skills()->attach($skillModel->id);
            }
        }

        $titles = [
            'AI Research Intern',
            'DevOps Engineer',
            'Marketing Analyst',
            'Frontend Developer',
            'Business Data Analyst'
        ];
        $industries = [
            'Technology', 'Finance', 'Marketing', 'Education', 'Healthcare'
        ];
        $interests = [
            'AI, analytics, cloud',
            'DevOps, Linux, web',
            'Marketing, UX, communication',
            'Frontend, design, UX',
            'Business, analytics, teamwork'
        ];
        $activities = [
            'Hackathons, research, open source',
            'DevOps meetups, cloud labs',
            'Marketing campaigns, design sprints',
            'Frontend bootcamps, designathons',
            'Business case competitions, analytics clubs'
        ];
        $courses = [
            'Computer Science',
            'Software Engineering',
            'Business Information Technology',
            'Information Technology',
            'Computer Engineering'
        ];
        $academicFields = [
            'Artificial Intelligence',
            'Machine Learning',
            'Data Science',
            'Cybersecurity',
            'Robotics'
        ];
        $years = ['1', '2', '3', '4', '5'];
        $universities = [
            'University of Nairobi',
            'Kenyatta University',
            'Strathmore University',
            'Jomo Kenyatta University',
            'Moi University'
        ];
        $orgNames = [
            'AI Society',
            'DevOps Guild',
            'Marketing Society',
            'Frontend Club',
            'Analytics Club'
        ];
        for ($i = 0; $i < 5; $i++) {
            Opportunity::create([
                'organization_id' => $orgs->random()->id,
                'category_id' => $categories->random()->id,
                'title' => $titles[$i],
                'description' => 'Exciting opportunity for ' . $titles[$i] . ' in ' . $industries[$i] . '.',
                'industry' => $industries[$i],
                'min_gpa' => (string) (2.5 + $i * 0.5),
                'course' => $courses[$i],
                'academic_field' => $academicFields[$i],
                'year' => $years[$i],
                'university' => $universities[$i],
                'interests' => $interests[$i],
                'activities' => $activities[$i],
                'organizations' => $orgNames[$i],
                'location' => 'City ' . chr(65 + $i),
                'type' => 'WBL',
                'deadline' => now()->addDays(15 + $i * 10),
                'status' => 'open',
                'monthly_stipend' => 300 + $i * 100,
                'min_hourly_rate' => 5 + $i,
                'max_hourly_rate' => 10 + $i * 2,
                'compensation_type' => 'stipend',
                'currency' => 'USD',
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
