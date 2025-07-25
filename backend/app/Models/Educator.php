<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Educator extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = ['name', 'email', 'password'];

    // Define the many-to-many relationship with StudentClass
    public function classes()
    {
        return $this->belongsToMany(StudentClass::class, 'class_educator', 'educator_id', 'class_id');
    }
    protected $hidden = [
        'password', 'remember_token',
    ];
}