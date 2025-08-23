<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Database\Seeder;

class UserRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        // Get the existing event owner
        $eventOwner = User::where('email', 'eventowner@example.com')->first();
        
        if (!$eventOwner) {
            $this->command->error('Event owner not found! Make sure DatabaseSeeder ran first.');
            return;
        }

        // Get existing test users
        $testUsers = User::whereIn('email', [
            'testuser1@example.com',
            'testuser2@example.com', 
            'testuser3@example.com'
        ])->get();

        foreach ($testUsers as $user) {
            // Create relationship between event owner and test user
            UserRelationship::firstOrCreate(
                [
                    'owner_id' => $eventOwner->id,
                    'user_id' => $user->id,
                ],
                [
                    'relationship_type' => 'created',
                    'invited_at' => now(),
                    'accepted_at' => now(),
                ]
            );
        }

        $this->command->info('Created test user relationships for event owner: ' . $eventOwner->email);
    }
} 