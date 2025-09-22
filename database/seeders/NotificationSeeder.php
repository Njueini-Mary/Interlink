<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                Notification::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(), // Ensure unique UUID for each record
                    'user_id' => $user->id,
                    'type' => 'info',
                    'title' => 'Welcome',
                    'message' => 'Welcome to the system!',
                ]);
            }
        }
    }
}
