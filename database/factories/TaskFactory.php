<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'project_id' => null,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'completed' => $this->faker->boolean(),
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
        ];
    }
}
