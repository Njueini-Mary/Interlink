<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
use App\Models\User;

// Factory for Student model.
// Ensures UUID is used for primary key.
// user_id must be set in the seeder to a valid student user.

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition()
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'university' => $this->faker->company . ' University',
            'course' => $this->faker->word(),
            'year' => $this->faker->numberBetween(1, 5),
            'resume' => null,
            'academic_field' => $this->faker->randomElement(['Engineering', 'Business', 'IT', 'Medicine', 'Law']),
            'is_available' => $this->faker->boolean(80),
            'rating' => $this->faker->randomFloat(2, 3, 5),
            'rating_count' => $this->faker->numberBetween(0, 100),
            'bio' => $this->faker->paragraph(),
            'profile_photo' => null,
            'metadata' => null,
        ];
    }
}
