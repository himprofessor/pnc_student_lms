<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'contact_info',
        'emergency_contact',
        'img',
    ];

    /**
     * The attributes that should be hidden for arrays or JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = ['role_name', 'display_name', 'initials', 'img_url'];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the role name attribute.
     */
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->name : 'No Role';
    }

    /**
     * Check if user has a specific role.
     */
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

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->hasRole(1);
    }

    /**
     * Check if user is teacher.
     */
    public function isTeacher()
    {
        return $this->hasRole('teacher') || $this->hasRole(2);
    }

    /**
     * Check if user is student.
     */
    public function isStudent()
    {
        return $this->hasRole('student') || $this->hasRole(3);
    }

    /**
     * Get user's full name or email if name is not available.
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: $this->email;
    }

    /**
     * Get user's initials for avatar.
     */
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

    /**
     * Get the full URL for the user's image.
     */
    public function getImgUrlAttribute()
    {
        if ($this->img) {
            // If it's already a full URL, return as is
            if (filter_var($this->img, FILTER_VALIDATE_URL)) {
                return $this->img;
            }
            
            // If it's a relative path, prepend the base URL
            return url('storage/' . $this->img);
        }
        
        return null;
    }

    /**
     * Check if user has a profile image.
     */
    public function hasProfileImage()
    {
        return !empty($this->img);
    }

/**
 * Update current user's profile
 */
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
            // Delete old image if exists
            if ($user->img && Storage::disk('public')->exists($user->img)) {
                Storage::disk('public')->delete($user->img);
            }
            
            // Store new image
            $imagePath = $request->file('img')->store('profile-images', 'public');
            $updateData['img'] = $imagePath;
        }
        
        // Only update if there's data to update
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
}