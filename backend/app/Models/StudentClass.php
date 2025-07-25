<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;

    protected $fillable = ['class_name']; // Add your actual class fields

    public function educators()
    {
        return $this->belongsToMany(Educator::class, 'class_educator', 'class_id', 'educator_id');
    }
}