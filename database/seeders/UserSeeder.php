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
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Organization users and records
        for ($i = 1; $i <= 5; $i++) {
            DB::transaction(function () use ($i) {
                $orgUserId = (string) Str::uuid();
                User::create([
                    'id' => $orgUserId,
                    'role' => 'organization',
                    'name' => 'Org User ' . $i,
                    'email' => 'org' . $i . '@example.com',
                    'password' => Hash::make('password'),
                ]);
                $orgId = (string) Str::uuid();
                \App\Models\Organization::create([
                    'id' => $orgId,
                    'user_id' => $orgUserId,
                    'name' => 'Organization ' . $i,
                    'profile' => 'Profile for Organization ' . $i,
                    'logo' => null,
                    'location' => 'City ' . $i,
                    'industry' => 'Industry ' . $i,
                    'website' => 'https://org' . $i . '.example.com',
                    'contact_email' => 'contact' . $i . '@org.com',
                    'contact_phone' => '+25470000000' . $i,
                    'rating' => rand(3, 5),
                    'rating_count' => rand(0, 100),
                    'metadata' => null,
                ]);
            });
        }

        // Student users and records
        for ($i = 1; $i <= 10; $i++) {
            DB::transaction(function () use ($i) {
                $studentUserId = (string) Str::uuid();
                User::create([
                    'id' => $studentUserId,
                    'role' => 'student',
                    'name' => 'Student User ' . $i,
                    'email' => 'student' . $i . '@example.com',
                    'password' => Hash::make('password'),
                ]);
                $studentId = (string) Str::uuid();
                \App\Models\Student::create([
                    'id' => $studentId,
                    'user_id' => $studentUserId,
                    'university' => 'University ' . $i,
                    'course' => 'Course ' . $i,
                    'year' => (string) rand(1, 5),
                    'resume' => null,
                    'academic_field' => 'Field ' . $i,
                    'is_available' => true,
                    'rating' => rand(3, 5),
                    'rating_count' => rand(0, 100),
                    'bio' => 'Bio for Student ' . $i,
                    'profile_photo' => null,
                    'metadata' => null,
                ]);
            });
        }
    }
}
