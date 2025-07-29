<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registerStudent(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|ends_with:@student.passerellesnumeriques.org',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 3, // Ensure this role ID is correct
        ]);

        return response()->json(['message' => 'Student account created successfully.']);
    }

    public function registerTeacher(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|ends_with:@passerellesnumeriques.org',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 2, // Assuming '2' is the ID for 'Teacher'
        ]);

        return response()->json(['message' => 'Teacher account created successfully.']);
    }

 public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Allow admin email without domain check
    if ($request->email !== 'admin@gmail.com') {
        if (
            !str_ends_with($request->email, '@student.passerellesnumeriques.org') &&
            !str_ends_with($request->email, '@passerellesnumeriques.org')
        ) {
            return response()->json(['message' => 'Invalid email domain'], 401);
        }
    }

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user  = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken;

    // Determine role
    if ($user->role_id == 1) {
        $role = 'admin';
    } elseif ($user->role_id == 2) {
        $role = 'teacher';
    } else {
        $role = 'student';
    }

    $dashboardUrl = match ($role) {
        'admin'   => '/admin-dashboard',
        'teacher' => '/educator-dashboard',
        default   => '/dashboard',
    };

    return response()->json([
        'token'          => $token,
        'message'        => 'Login successful',
        'role'           => $role,
        'dashboard_url'  => $dashboardUrl,
        'user'           => [
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role_id' => $user->role_id,
        ],
    ], 200);
}


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
