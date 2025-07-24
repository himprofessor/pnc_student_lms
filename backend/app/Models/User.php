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
    ];

    protected $appends = ['role_name', 'display_name', 'initials', 'img_url'];

    // Relations

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Role helpers

    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->name : 'No Role';
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

    // Accessors

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

    /**
     * Return full URL of user image or null if no image
     */
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

    /**
     * Check if profile image exists physically in storage
     */
    public function hasProfileImage()
    {
        return $this->img && Storage::disk('public')->exists($this->img);
    }

    /**
     * Update user's profile image, delete old one if exists
     */
    public function updateProfileImage($imagePath)
    {
        if ($this->hasProfileImage()) {
            Storage::disk('public')->delete($this->img);
        }
        $this->img = $imagePath;
        $this->save();
    }

    /**
     * Remove profile image from storage and clear DB column
     */
    public function removeProfileImage()
    {
        if ($this->hasProfileImage()) {
            Storage::disk('public')->delete($this->img);
        }
        $this->img = null;
        $this->save();
    }

    /**
     * Update profile info (except password and image) from request
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
                'img' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = collect($validator->validated())->except(['img'])->toArray();

            // Handle image upload separately
            if ($request->hasFile('img')) {
                if ($user->hasProfileImage()) {
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
     * (Optional) format contact info display
     */
    public function getContactInfoFormatted()
    {
        return $this->contact_info ?: 'No contact info provided';
    }

    /**
     * Dummy method to check if user active
     */
    public function isActive()
    {
        return true;
    }
}
