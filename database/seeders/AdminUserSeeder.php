<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@interlink.test',
        ], [
            'id' => (string) Str::uuid(),
            'name' => 'System Admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        // No need to assign Spatie role; use 'role' column only for consistency
    }
}
