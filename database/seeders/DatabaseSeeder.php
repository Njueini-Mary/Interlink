<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Run role and admin seeders first
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);

        // UserSeeder must run before OrganizationSeeder to ensure valid foreign keys
        $this->call([
            UserSeeder::class,
            OrganizationSeeder::class,
            StudentSeeder::class,
            ActivitySeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
