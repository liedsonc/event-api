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
        // First create the roles
        $this->call([
            RoleSeeder::class,
            UserRelationshipSeeder::class,
        ]);

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
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminUser->roles()->sync([$adminRole->id]);
        }

        // Create some additional test users with factory
        // User::factory(5)->create();

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin user: admin@example.com / password');
        $this->command->info('Event owner: eventowner@example.com / password');
        $this->command->info('Test users: testuser1@example.com, testuser2@example.com, testuser3@example.com / password');
    }
}
