<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
{
    \DB::table('student_classes')->insert([
        ['name' => 'Class A', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Class B', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Web A', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Web B', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Web C', 'created_at' => now(), 'updated_at' => now()],

    ]);
}

}
