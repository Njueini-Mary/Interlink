<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organization;
use App\Models\User;

// Factory for Organization model.
// Ensures UUID is used for primary key.
// user_id must be set in the seeder to a valid organization user.

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition()
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => $this->faker->company(),
            'profile' => $this->faker->paragraph(),
            'logo' => null,
            'location' => $this->faker->city(),
            'industry' => $this->faker->randomElement(['Tech', 'Finance', 'Healthcare', 'Education', 'Manufacturing']),
            'website' => $this->faker->url(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'rating' => $this->faker->randomFloat(2, 3, 5),
            'rating_count' => $this->faker->numberBetween(0, 100),
            'metadata' => null,
        ];
    }
}
