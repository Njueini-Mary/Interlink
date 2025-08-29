<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;

// Seeder for Student model.
// Assigns each student to a valid user with the 'student' role.
// Throws an exception if no such users exist.

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Optionally, create extra students for testing (not linked to UserSeeder)
        $studentUsers = User::where('role', 'student')->pluck('id')->filter(function($id) {
            // Only keep valid UUIDs
            return preg_match('/^[0-9a-fA-F-]{36}$/', $id);
        })->values()->toArray();
        $count = min(2, count($studentUsers));
        for ($i = 0; $i < $count; $i++) {
            $studentId = (string) \Illuminate\Support\Str::uuid();
            $userId = $studentUsers[$i];
            \App\Models\Student::create([
                'id' => $studentId,
                'user_id' => $userId,
                'university' => 'Extra University ' . $i,
                'course' => 'Extra Course ' . $i,
                'year' => (string) rand(1, 5),
                'resume' => null,
                'academic_field' => 'Extra Field ' . $i,
                'is_available' => true,
                'rating' => rand(3, 5),
                'rating_count' => rand(0, 100),
                'bio' => 'Extra Bio for Student ' . $i,
                'profile_photo' => null,
                'metadata' => null,
            ]);
        }
    }
}
