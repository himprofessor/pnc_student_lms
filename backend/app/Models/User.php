<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'contact_info',
        'emergency_contact',
        'img',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = ['role_name', 'display_name', 'initials', 'img_url'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
      // Relationship to Educator
    public function educator()
    {
        return $this->hasOne(Educator::class);
    }

    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->name : 'No Role';
    }
    public function studentClasses()
    {
        return $this->belongsToMany(StudentClass::class, 'class_student', 'user_id', 'student_class_id');
    }
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->role && $this->role->name === $role;
        }

        if (is_numeric($role)) {
            return $this->role_id == $role;
        }

        return false;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->hasRole(1);
    }

    public function isTeacher()
    {
        return $this->hasRole('teacher') || $this->hasRole(2);
    }

    public function isStudent()
    {
        return $this->hasRole('student') || $this->hasRole(3);
    }

    public function getDisplayNameAttribute()
    {
        return $this->name ?: $this->email;
    }

    public function getInitialsAttribute()
    {
        $name = $this->name ?: $this->email;

        if (strpos($name, '@') !== false) {
            return strtoupper(substr($name, 0, 1));
        }

        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 1));
    }

    public function getImgUrlAttribute()
    {
        if ($this->img) {
            if (filter_var($this->img, FILTER_VALIDATE_URL)) {
                return $this->img;
            }

            return url('storage/' . $this->img);
        }

        return null;
    }

    public function hasProfileImage()
    {
        return !empty($this->img);
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'contact_info' => 'sometimes|nullable|string|max:255',
                'emergency_contact' => 'sometimes|nullable|string|max:255',
                'img' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Start with validated data (excluding image)
            $updateData = collect($validator->validated())->except(['img'])->toArray();

            // Handle image upload separately
            if ($request->hasFile('img')) {
                if ($user->img && Storage::disk('public')->exists($user->img)) {
                    Storage::disk('public')->delete($user->img);
                }

                $imagePath = $request->file('img')->store('profile-images', 'public');
                $updateData['img'] = $imagePath;
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            $user->refresh();
            $user->load('role');

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if the user has access to the teacher dashboard.
     */
    public function canAccessTeacherDashboard()
    {
        return $this->isTeacher() && str_ends_with($this->email, '@passerellesnumeriques.org');
    }
    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'class_teacher', 'teacher_id', 'class_id');
    }
      public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'id');
    }


    /**
     * Check if the user has access to the student dashboard.
     */
    public function canAccessStudentDashboard()
    {
        return $this->isStudent() && str_ends_with($this->email, '@student.passerellesnumeriques.org');
    }
}