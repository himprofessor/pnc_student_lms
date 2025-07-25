<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Educator extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}