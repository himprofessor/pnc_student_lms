<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 1,  // adjust role_id based on your roles table
            'phone' => '0123456789',
        ]);

        User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2,  // adjust role_id based on your roles table
            'phone' => '0987654321',
        ]);

        User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 3,  // adjust role_id based on your roles table
            'phone' => '0112233445',
        ]);
    }
}
