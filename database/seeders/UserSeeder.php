<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

// Seeder for User model.
// Creates admin, organization, and student users with UUIDs as primary keys.
// Assigns appropriate roles after creation.

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        $adminId = (string) Str::uuid();
        User::create([
            'id' => $adminId,
            'role' => 'admin',
            'name' => 'Admin User',
            'email' => 'john@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // --- Add a "perfect match" student profile ---
        $perfectStudentId = (string) Str::uuid();
        $perfectStudentUser = User::create([
            'id' => $perfectStudentId,
            'role' => 'student',
            'name' => 'Aisha Mwangi',
            'email' => 'aisha.mwangi@example.com',
            'password' => Hash::make('password'),
        ]);
        $perfectStudent = Student::create([
            'user_id' => $perfectStudentId,
            'university' => 'University of Nairobi',
            'course' => 'Computer Science',
            'academic_field' => 'Data Science',
            'bio' => 'Passionate about AI, Data Science, and open source. Winner of the Nairobi Data Challenge. Member of Data Science Society.',
            'interests' => 'AI, Data Science, Python',
            'gpa' => 3.8,
            'year' => 4,
            'is_available' => true,
            'rating' => 5.0,
            'rating_count' => 50,
            'resume' => 'aisha_resume.pdf',
            'activities' => 'Hackathons, research, open source',
            'organizations' => 'Data Science Society',
        ]);
        $perfectSkills = ['AI', 'Data Science', 'Python', 'Linux'];
        foreach ($perfectSkills as $skill) {
            $skillModel = \App\Models\Skill::firstOrCreate(['name' => $skill]);
            $perfectStudent->skills()->attach($skillModel->id);
        }

        // Organization users and records (10 realistic organizations)
        $orgNames = [
            'Nairobi Tech Hub',
            'Data Science Society',
            'Green Energy Solutions',
            'Mombasa Fintech Group',
            'Kenya Health Innovators',
            'AgriTech Africa',
            'Smart Logistics Ltd',
            'Creative Design Studio',
            'East Africa Robotics',
            'EduTech Kenya'
        ];
        $orgCities = [
            'Nairobi', 'Nairobi', 'Eldoret', 'Mombasa', 'Nairobi', 'Kisumu', 'Nairobi', 'Nairobi', 'Nairobi', 'Nakuru'
        ];
        $orgIndustries = [
            'Technology', 'Data Science', 'Energy', 'Finance', 'Healthcare', 'Agriculture', 'Logistics', 'Design', 'Robotics', 'Education'
        ];
        for ($i = 0; $i < 10; $i++) {
            DB::transaction(function () use ($i, $orgNames, $orgCities, $orgIndustries) {
                $orgUserId = (string) Str::uuid();
                User::create([
                    'id' => $orgUserId,
                    'role' => 'organization',
                    'name' => $orgNames[$i] . ' Admin',
                    'email' => strtolower(str_replace(' ', '', $orgNames[$i])) . '@example.com',
                    'password' => Hash::make('password'),
                ]);
                $orgId = (string) Str::uuid();
                \App\Models\Organization::create([
                    'id' => $orgId,
                    'user_id' => $orgUserId,
                    'name' => $orgNames[$i],
                    'profile' => 'Leading ' . $orgIndustries[$i] . ' organization in ' . $orgCities[$i] . '.',
                    'logo' => null,
                    'location' => $orgCities[$i],
                    'industry' => $orgIndustries[$i],
                    'website' => 'https://' . strtolower(str_replace(' ', '', $orgNames[$i])) . '.org',
                    'contact_email' => 'contact@' . strtolower(str_replace(' ', '', $orgNames[$i])) . '.org',
                    'contact_phone' => '+2547000000' . ($i+1),
                    'rating' => rand(3, 5),
                    'rating_count' => rand(0, 100),
                    'metadata' => null,
                ]);
            });
        }

        // Student users and records
        $studentProfiles = [
                [
                    'name' => 'Alice Kimani',
                    'email' => 'alice.kimani@example.com',
                    'university' => 'University of Nairobi',
                    'course' => 'Computer Science',
                    'academic_field' => 'Artificial Intelligence',
                    'bio' => 'AI enthusiast and Python developer. Loves hackathons and open source.',
                ],
                [
                    'name' => 'Brian Otieno',
                    'email' => 'brian.otieno@example.com',
                    'university' => 'Kenyatta University',
                    'course' => 'Software Engineering',
                    'academic_field' => 'Machine Learning',
                    'bio' => 'Web developer and DevOps fan. Enjoys cloud labs and Linux.',
                ],
                [
                    'name' => 'Cynthia Mwangi',
                    'email' => 'cynthia.mwangi@example.com',
                    'university' => 'Strathmore University',
                    'course' => 'Business Information Technology',
                    'academic_field' => 'Data Science',
                    'bio' => 'Data scientist with a passion for analytics and teamwork.',
                ],
                [
                    'name' => 'David Njoroge',
                    'email' => 'david.njoroge@example.com',
                    'university' => 'Jomo Kenyatta University',
                    'course' => 'Information Technology',
                    'academic_field' => 'Cybersecurity',
                    'bio' => 'Cybersecurity student and open source contributor.',
                ],
                [
                    'name' => 'Evelyn Wambui',
                    'email' => 'evelyn.wambui@example.com',
                    'university' => 'Moi University',
                    'course' => 'Computer Engineering',
                    'academic_field' => 'Robotics',
                    'bio' => 'Robotics and AI hobbyist. Loves designathons and research.',
                ],
                [
                    'name' => 'Felix Ochieng',
                    'email' => 'felix.ochieng@example.com',
                    'university' => 'Egerton University',
                    'course' => 'Electrical Engineering',
                    'academic_field' => 'Embedded Systems',
                    'bio' => 'Embedded systems tinkerer and analytics club member.',
                ],
                [
                    'name' => 'Grace Achieng',
                    'email' => 'grace.achieng@example.com',
                    'university' => 'Maseno University',
                    'course' => 'Mathematics and Computer Science',
                    'academic_field' => 'Statistics',
                    'bio' => 'Statistics lover and business case competitor.',
                ],
                [
                    'name' => 'Henry Kiptoo',
                    'email' => 'henry.kiptoo@example.com',
                    'university' => 'Technical University of Kenya',
                    'course' => 'Mechatronics',
                    'academic_field' => 'Automation',
                    'bio' => 'Mechatronics student, design club, and hackathon regular.',
                ],
                [
                    'name' => 'Irene Muthoni',
                    'email' => 'irene.muthoni@example.com',
                    'university' => 'Dedan Kimathi University',
                    'course' => 'Computer Science',
                    'academic_field' => 'Cloud Computing',
                    'bio' => 'Cloud computing enthusiast and Google DSC member.',
                ],
                [
                    'name' => 'James Mwenda',
                    'email' => 'james.mwenda@example.com',
                    'university' => 'Pwani University',
                    'course' => 'Software Engineering',
                    'academic_field' => 'Mobile Development',
                    'bio' => 'Mobile developer and Linux Foundation member.',
                ],
            ];
        $gpas = ['3.8','3.2','3.5','3.0','3.7','3.1','3.6','3.3','3.4','3.9'];
        $years = [4,3,4,2,4,3,4,2,3,4];
        $is_available = [true,true,false,true,true,false,true,true,false,true];
        $ratings = [5.0,4.2,4.5,3.8,4.7,4.1,4.6,4.3,4.4,4.9];
        $rating_counts = [50,20,30,10,25,12,18,15,22,40];
        $resumes = ['alice_resume.pdf','brian_resume.pdf','cynthia_resume.pdf','david_resume.pdf','evelyn_resume.pdf','felix_resume.pdf','grace_resume.pdf','henry_resume.pdf','irene_resume.pdf','james_resume.pdf'];
        $interests = [
            'AI, analytics, cloud, Python',
            'DevOps, Linux, web development',
            'Machine learning, statistics, problem solving',
            'UX, marketing, communication',
            'SQL, R, business analytics, teamwork',
            'Embedded systems, IoT, robotics',
            'Business case competitions, statistics',
            'Automation, mechatronics, design',
            'Cloud computing, Google DSC, teamwork',
            'Mobile development, Linux, open source'
        ];
        $activities = [
                'Hackathons, research, open source',
                'DevOps meetups, cloud labs',
                'Marketing campaigns, design sprints',
                'Frontend bootcamps, designathons',
                'Business case competitions, analytics clubs',
                'IoT expos, robotics workshops',
                'Statistics seminars, business hackathons',
                'Mechatronics labs, design club',
                'Cloud summits, DSC events',
                'Mobile app hackathons, Linux meetups'
            ];
        $organizations = [
                'IEEE, Google DSC',
                'Linux Foundation',
                'Marketing Society',
                'Design Club',
                'Analytics Club',
                'Robotics Society',
                'Business Club',
                'Design Club',
                'Google DSC',
                'Linux Foundation'
            ];
        $skillsList = [
            ['AI', 'Python', 'Linux'],
            ['DevOps', 'Linux', 'Web Development'],
            ['Machine Learning', 'Statistics', 'Analytics'],
            ['Cybersecurity', 'Open Source'],
            ['Robotics', 'Design'],
            ['Embedded Systems', 'IoT', 'Robotics'],
            ['Statistics', 'Business'],
            ['Automation', 'Mechatronics', 'Design'],
            ['Cloud Computing', 'Teamwork'],
            ['Mobile Development', 'Linux', 'Open Source']
        ];
        $opportunities = \App\Models\Opportunity::all();
        for ($i = 1; $i <= 10; $i++) {
            DB::transaction(function () use ($i, $studentProfiles, $gpas, $years, $is_available, $ratings, $rating_counts, $resumes, $interests, $activities, $organizations, $skillsList, $opportunities) {
                $profile = $studentProfiles[$i-1];
                $studentUserId = (string) Str::uuid();
                $user = User::create([
                    'id' => $studentUserId,
                    'role' => 'student',
                    'name' => $profile['name'],
                    'email' => $profile['email'],
                    'password' => Hash::make('password'),
                ]);
                $student = Student::create([
                    'user_id' => $studentUserId,
                    'university' => $profile['university'],
                    'course' => $profile['course'],
                    'academic_field' => $profile['academic_field'],
                    'bio' => $profile['bio'],
                    'interests' => $interests[$i-1],
                    'gpa' => $gpas[$i-1],
                    'year' => $years[$i-1],
                    'is_available' => $is_available[$i-1],
                    'rating' => $ratings[$i-1],
                    'rating_count' => $rating_counts[$i-1],
                    'resume' => $resumes[$i-1],
                    'activities' => $activities[$i-1],
                    'organizations' => $organizations[$i-1],
                ]);
                // Attach skills
                foreach ($skillsList[$i-1] as $skill) {
                    $skillModel = \App\Models\Skill::firstOrCreate(['name' => $skill]);
                    $student->skills()->attach($skillModel->id);
                }
                // Seed an application for this student to a random opportunity
                if ($opportunities->count() > 0) {
                    $opp = $opportunities->random();
                    \App\Models\Application::create([
                        'id' => (string) Str::uuid(),
                        'student_id' => $student->id,
                        'opportunity_id' => $opp->id,
                        'status' => 'submitted',
                        'created_at' => now()->subDays(rand(0, 30)),
                        'updated_at' => now()->subDays(rand(0, 30)),
                        'match_score' => rand(40, 90),
                        'tfidf_score' => rand(40, 90),
                        'logreg_score' => rand(40, 90),
                        'rf_score' => rand(40, 90),
                    ]);
                }
            });
        }
    }
}
