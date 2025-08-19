<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Database\Seeder;

class UserRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create an event owner
        $eventOwner = User::firstOrCreate(
            ['email' => 'eventowner@example.com'],
            [
                'name' => 'Event Owner',
                'password' => bcrypt('password'),
            ]
        );

        // Assign event_owner role to the event owner
        $eventOwnerRole = \App\Models\Role::where('name', 'event_owner')->first();
        if ($eventOwnerRole) {
            $eventOwner->roles()->sync([$eventOwnerRole->id]);
        }

        // Get or create some test users
        $testUsers = [
            [
                'name' => 'Test User 1',
                'email' => 'testuser1@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Test User 2',
                'email' => 'testuser2@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Test User 3',
                'email' => 'testuser3@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign event_owner role to test users so they can access the event owner panel
            $eventOwnerRole = \App\Models\Role::where('name', 'event_owner')->first();
            if ($eventOwnerRole) {
                $user->roles()->sync([$eventOwnerRole->id]);
            }

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