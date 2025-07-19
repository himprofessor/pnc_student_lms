<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypesSeeder extends Seeder
{
    public function run()
    {
        LeaveType::create(['name' => 'Sick Leave']);
        LeaveType::create(['name' => 'Personal Leave']);
        LeaveType::create(['name' => 'Vacation Leave']);
        LeaveType::create(['name' => 'Emergency Leave']);
        LeaveType::create(['name' => 'Maternity Leave']);
        LeaveType::create(['name' => 'Paternity Leave']);
        LeaveType::create(['name' => 'Bereavement Leave']);
        LeaveType::create(['name' => 'Unpaid Leave']);
        LeaveType::create(['name' => 'Compassionate Leave']);
        LeaveType::create(['name' => 'Study Leave']);
        LeaveType::create(['name' => 'Public Holiday Leave']);
        LeaveType::create(['name' => 'Personal Leave']);
    }
}