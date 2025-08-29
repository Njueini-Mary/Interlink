<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['info', 'alert', 'reminder']),
            'title' => $this->faker->sentence(3),
            'message' => $this->faker->sentence(8),
            'read_at' => null,
        ];
    }
}
