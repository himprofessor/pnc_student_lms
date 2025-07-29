<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Educator extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = ['name', 'email', 'password', 'user_id'];

    // Define the many-to-many relationship with StudentClass
    public function classes()
    {
        return $this->belongsToMany(StudentClass::class, 'class_educator', 'educator_id', 'class_id');
    }

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'user_id'; // Tell Laravel to use 'user_id' for route model binding
    }
}