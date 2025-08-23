<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First create the roles
        $this->call([
            RoleSeeder::class,
        ]);

        // Get the roles we need
        $adminRole = Role::where('name', 'admin')->first();
        $eventOwnerRole = Role::where('name', 'event_owner')->first();

        if (!$adminRole || !$eventOwnerRole) {
            $this->command->error('Roles not found! Make sure RoleSeeder ran successfully.');
            return;
        }

        // Create an admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]
        );

        // Assign admin role
        $adminUser->roles()->sync([$adminRole->id]);

        // Create event owner user
        $eventOwner = User::firstOrCreate(
            ['email' => 'eventowner@example.com'],
            [
                'name' => 'Event Owner',
                'email' => 'eventowner@example.com',
                'password' => bcrypt('password'),
            ]
        );

        // Assign event_owner role to the event owner
        $eventOwner->roles()->sync([$eventOwnerRole->id]);

        // Also give admin role to event owner so they can access both panels
        $eventOwner->roles()->attach($adminRole->id);

        // Create test users
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

            // Assign event_owner role to test users
            $user->roles()->sync([$eventOwnerRole->id]);
        }

        // Now create user relationships
        $this->call([
            UserRelationshipSeeder::class,
        ]);

        // Verify roles were assigned correctly
        $this->command->info('Verifying role assignments...');
        $this->command->info('Admin user roles: ' . $adminUser->roles->pluck('name')->implode(', '));
        $this->command->info('Event owner roles: ' . $eventOwner->roles->pluck('name')->implode(', '));

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin user: admin@example.com / password');
        $this->command->info('Event owner (also admin): eventowner@example.com / password');
        $this->command->info('Test users: testuser1@example.com, testuser2@example.com, testuser3@example.com / password');
    }
}
