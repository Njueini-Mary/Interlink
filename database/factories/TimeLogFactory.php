<?php

namespace Database\Factories;

use App\Models\TimeLog;
use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeLogFactory extends Factory
{
    protected $model = TimeLog::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'hours' => $this->faker->numberBetween(1, 8),
        ];
    }
}
