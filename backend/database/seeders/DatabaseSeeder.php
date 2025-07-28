<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ 1. Create roles first
        Role::insert([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'teacher'],
            ['id' => 3, 'name' => 'student'],
        ]);

        // ✅ 2. Create default admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@passerellesnumeriques.org',
            'password' => bcrypt('password123'),
            'role_id' => 1, // admin
        ]);

        // ✅ 3. Create a default teacher (optional)
        User::create([
            'name' => 'Teacher',
            'email' => 'teacher@passerellesnumeriques.org',
            'password' => bcrypt('password123'),
            'role_id' => 2, // teacher
        ]);

        // ✅ 4. Create a default student (optional)
        User::create([
            'name' => 'Sela Torm',
            'email' => 'sela.torm@student.passerellesnumeriques.org',
            'password' => bcrypt('password123'),
            'role_id' => 3, // student
        ]);
    }
}
