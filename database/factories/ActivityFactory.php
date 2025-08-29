<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition()
    {
        return [
            'action' => $this->faker->randomElement(['login', 'update', 'create', 'delete']),
            'description' => $this->faker->sentence(),
            'data' => [],
        ];
    }
}
