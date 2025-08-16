<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

    /**
     * Create a new student account.
     * This method is added directly to the model for demonstration,
     * but it's generally better to place such logic in a service or controller.
     *
     * @param array $data The data for the new student (name, password, password_confirmation)
     * @return User
     * @throws ValidationException
     */
   public function createStudentAccount(array $data)
{
    $validator = validator($data, [
        'name' => 'required|string|max:255',
        'password' => 'required|string|min:8', // Removed 'confirmed' for CSV compatibility
    ]);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    $email_prefix = Str::slug($data['name'], '.');
    $email = $email_prefix . '@student.passerellesnumeriques.org';

    if (User::where('email', $email)->exists()) {
        throw ValidationException::withMessages([
            'name' => ['A user with this name already exists. Please choose a different name.'],
        ]);
    }

    $student = User::create([
        'name' => $data['name'],
        'email' => $email,
        'password' => Hash::make($data['password']),
        'role_id' => 3,
    ]);

    return $student;
}
}