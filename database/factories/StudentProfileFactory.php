<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentProfileFactory extends Factory
{
    protected $model = StudentProfile::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'academic_field' => $this->faker->word(),
            'bio' => $this->faker->paragraph(),
            'profile_photo' => null,
            'resume' => null,
            'is_available' => $this->faker->boolean(80),
            'rating' => $this->faker->randomFloat(2, 3, 5),
        ];
    }
}
