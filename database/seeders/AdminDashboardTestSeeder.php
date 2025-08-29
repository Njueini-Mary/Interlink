<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Review;
use App\Models\Skill;
use App\Models\Task;
use App\Models\TimeLog;
use App\Models\User;

class AdminDashboardTestSeeder extends Seeder
{
    public function run()
    {
        // Categories
        Category::factory()->count(3)->create();

        // Skills
        Skill::factory()->count(3)->create();

        // Tasks
        Task::factory()->count(3)->create();

        // Reviews (requires users)
        $users = User::inRandomOrder()->take(2)->get();
        if ($users->count() >= 2) {
            Review::factory()->count(3)->create([
                'freelancer_id' => $users[0]->id,
                'client_id' => $users[1]->id,
            ]);
        }

        // TimeLogs (requires users and tasks)
        $task = Task::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        if ($task && $user) {
            TimeLog::factory()->count(3)->create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'hours' => rand(1,8),
            ]);
        }
    }
}
