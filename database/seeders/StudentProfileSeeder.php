<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentProfile;
use App\Models\User;

class StudentProfileSeeder extends Seeder
{
    public function run()
    {
        StudentProfile::factory()->count(20)->create();
    }
}
