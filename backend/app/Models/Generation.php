<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'year'];

    // A generation has many students
    public function students()
    {
        return $this->hasMany(User::class, 'generation_id')->where('role_id', 3);
    }
    

}
