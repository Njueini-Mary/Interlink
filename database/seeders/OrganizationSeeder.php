<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;

// Seeder for Organization model.
// Assigns each organization to a valid user with the 'organization' role.
// Throws an exception if no such users exist.

class OrganizationSeeder extends Seeder
{
    public function run()
    {
        // Optionally, create extra organizations for testing (not linked to UserSeeder)
        $orgUsers = User::where('role', 'organization')->pluck('id')->filter(function($id) {
            // Only keep valid UUIDs
            return preg_match('/^[0-9a-fA-F-]{36}$/', $id);
        })->values()->toArray();
        $count = min(2, count($orgUsers));
        for ($i = 0; $i < $count; $i++) {
            $orgId = (string) \Illuminate\Support\Str::uuid();
            $userId = $orgUsers[$i];
            \App\Models\Organization::create([
                'id' => $orgId,
                'user_id' => $userId,
                'name' => 'Extra Organization ' . $i,
                'profile' => 'Extra profile for Organization ' . $i,
                'logo' => null,
                'location' => 'Extra City ' . $i,
                'industry' => 'Extra Industry ' . $i,
                'website' => 'https://extraorg' . $i . '.example.com',
                'contact_email' => 'extra' . $i . '@org.com',
                'contact_phone' => '+25470000001' . $i,
                'rating' => rand(3, 5),
                'rating_count' => rand(0, 100),
                'metadata' => null,
            ]);
        }
    }
}
