<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    protected $fillable = ['name'];

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_student', 'student_class_id', 'user_id');
    }

    public function educators()
    {
        return $this->belongsToMany(Educator::class, 'class_educator', 'class_id', 'educator_id');
    }
}
