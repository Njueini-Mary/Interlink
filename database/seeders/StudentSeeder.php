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
        // No-op: all students are now seeded with realistic data in UserSeeder
    }
}
