// app/Models/Student.php
<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'user_id']; // Ensure user_id is here

    public function user()
    {
        // This defines the one-to-one relationship where Student belongs to a User
        // 'user_id' is the foreign key on the 'students' table
        // 'id' is the primary key on the 'users' table
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function student_classes()
    {
        return $this->belongsToMany(StudentClass::class, 'class_student', 'student_id', 'class_id');
    }

    public function getRouteKeyName()
    {
        return 'user_id'; // Crucial for Route Model Binding
    }
}