<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'reason', 'from_date', 'to_date', 'status'];

    // Define the relationship with the User model
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}