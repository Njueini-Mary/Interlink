<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                Activity::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(), // Ensure unique UUID for each record
                    'user_id' => $user->id,
                    'action' => 'login',
                    'description' => 'User logged in',
                    'model_type' => get_class($user),
                    'model_id' => $user->id,
                ]);
            }
        }
    }
}
