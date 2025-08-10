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
        // 1. Validate the incoming data
        $validator = validator($data, [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // 2. Generate the student's email address
        $email_prefix = Str::slug($data['name'], '.');
        $email = $email_prefix . '@student.passerellesnumeriques.org';

        // 3. Check if the generated email already exists
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'name' => ['A user with this name already exists. Please choose a different name.'],
            ]);
        }

        // 4. Create the new user account using the User model
        $student = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'role_id' => 3, // Assuming role 3 corresponds to 'student' based on your isStudent() method
        ]);

        return $student;
    }
}