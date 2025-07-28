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
            'img' => 'profile-images/bCWcOcLPrlZIw1zWaEXyCMXBW6jSuJ4hNpudIvwo.jpg', // Use existing profile image
        ]);

        User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2,  // adjust role_id based on your roles table
            'phone' => '0987654321',
            'img' => 'profile-images/E4ymsB2VMGuXLKcFFbWJaNJY8OqINBYjTv1Hptst.jpg', // Use existing profile image
        ]);

        User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 3,  // adjust role_id based on your roles table
            'phone' => '0112233445',
            'img' => 'profile-images/faCR2Sy3Wn7WhbEP9bUO3QPz5545vhYG3UHRYnNN.jpg', // Use existing profile image
        ]);
    }
}
