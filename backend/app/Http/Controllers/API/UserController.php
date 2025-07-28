<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Update only profile image (using helper in User.php)
     */
    public function updateOnlyProfileImage(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $imagePath = $request->file('img')->store('profile-images', 'public');
            $user->updateProfileImage($imagePath); // Helper method in User model

            return response()->json([
                'success' => true,
                'message' => 'Profile image updated successfully',
                'data' => $user,
                'img_url' => $user->img_url
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating profile image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete only profile image (uses helper in User.php)
     */
    public function deleteOnlyProfileImage(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->hasProfileImage()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No profile image to delete'
                ], 404);
            }

            $user->removeProfileImage(); // Helper method in User model

            return response()->json([
                'success' => true,
                'message' => 'Profile image deleted successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting profile image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting profile image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get formatted contact info (optional new endpoint)
     */
    public function getFormattedContactInfo(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'contact_info' => $user->getContactInfoFormatted(), // Helper method in User model
        ], 200);
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $user->load('role');

            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Error in getCurrentUser: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Update current user's profile (FULL version, keep this one!)
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

            $updateData = $validator->validated();

            if ($request->hasFile('img')) {
                if ($user->img && Storage::disk('public')->exists($user->img)) {
                    Storage::disk('public')->delete($user->img);
                }

                $imagePath = $request->file('img')->store('profile-images', 'public');
                $updateData['img'] = $imagePath;
            }

            $user->update($updateData);
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
     * Upload profile image
     */
    public function uploadProfileImage(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($user->img && Storage::disk('public')->exists($user->img)) {
                Storage::disk('public')->delete($user->img);
            }

            $imagePath = $request->file('img')->store('profile-images', 'public');

            $user->update(['img' => $imagePath]);
            $user->load('role');

            return response()->json([
                'success' => true,
                'message' => 'Profile image uploaded successfully',
                'data' => $user,
                'img_url' => $user->img_url
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading profile image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading profile image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete profile image
     */
    public function deleteProfileImage(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->img && Storage::disk('public')->exists($user->img)) {
                Storage::disk('public')->delete($user->img);
            }

            $user->update(['img' => null]);
            $user->load('role');

            return response()->json([
                'success' => true,
                'message' => 'Profile image deleted successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting profile image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting profile image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');

            $query = User::with('role');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8',
                'role_id' => 'required_without:role|exists:roles,id',
                'role' => 'required_without:role_id|string|exists:roles,name',
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

            $roleId = $request->role_id;
            if ($request->has('role') && !$request->has('role_id')) {
                $role = Role::where('name', $request->role)->first();
                $roleId = $role->id;
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => $roleId,
                'contact_info' => $request->contact_info,
                'emergency_contact' => $request->emergency_contact,
            ];

            if ($request->hasFile('img')) {
                $imagePath = $request->file('img')->store('profile-images', 'public');
                $userData['img'] = $imagePath;
            }

            $user = User::create($userData);
            $user->load('role');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific user
     */
    public function show(string $id)
    {
        try {
            $user = User::with('role')->find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user (admin or self)
     */
    public function update(Request $request, string $id = null)
    {
        try {
            $user = $id ? User::find($id) : $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|nullable|string|min:8',
                'role_id' => 'sometimes|required|exists:roles,id',
                'role' => 'sometimes|required|string|exists:roles,name',
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

            $updateData = [];

            if ($request->has('name')) $updateData['name'] = $request->name;
            if ($request->has('email')) $updateData['email'] = $request->email;
            if ($request->filled('password')) $updateData['password'] = Hash::make($request->password);
            if ($request->has('role_id')) $updateData['role_id'] = $request->role_id;
            elseif ($request->has('role')) {
                $role = Role::where('name', $request->role)->first();
                $updateData['role_id'] = $role->id;
            }
            if ($request->has('contact_info')) $updateData['contact_info'] = $request->contact_info;
            if ($request->has('emergency_contact')) $updateData['emergency_contact'] = $request->emergency_contact;

            if ($request->hasFile('img')) {
                if ($user->img && Storage::disk('public')->exists($user->img)) {
                    Storage::disk('public')->delete($user->img);
                }
                $imagePath = $request->file('img')->store('profile-images', 'public');
                $updateData['img'] = $imagePath;
            }

            if (!empty($updateData)) $user->update($updateData);

            $user->refresh()->load('role');

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            $user->update(['password' => Hash::make($request->new_password)]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user
     */
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if (auth()->id() == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 422);
            }

            if ($user->img && Storage::disk('public')->exists($user->img)) {
                Storage::disk('public')->delete($user->img);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ids = array_filter($request->ids, fn($id) => $id != auth()->id());

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid users to delete'
                ], 422);
            }

            $usersToDelete = User::whereIn('id', $ids)->get();
            foreach ($usersToDelete as $user) {
                if ($user->img && Storage::disk('public')->exists($user->img)) {
                    Storage::disk('public')->delete($user->img);
                }
            }

            $deletedCount = User::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} users deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error bulk deleting users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
