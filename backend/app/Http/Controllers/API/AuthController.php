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
    // Validate incoming request
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    // Check email domain
    if (!str_ends_with($request->email, '@student.passerellesnumeriques.org') &&
        !str_ends_with($request->email, '@passerellesnumeriques.org')) {
        return response()->json(['error' => ['email' => ['Invalid email domain']]], 401);
    }

    // Attempt to authenticate the user
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['error' => ['password' => ['Invalid credentials']]], 401);
    }

    // Retrieve the authenticated user
    $user = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken;

    // Check if the user is a teacher and respond accordingly
    if ($user->role_id == 2) { // Assuming '2' is the ID for 'Teacher'
        return response()->json([
            'token' => $token,
            'message' => 'Login successful',
            'role' => 'teacher',
            'dashboard_url' => '/teacher-dashboard' // URL for the teacher's dashboard
        ]);
    }

    return response()->json([
        'token' => $token,
        'message' => 'Login successful',
        'role' => 'student',
        'dashboard_url' => '/student-dashboard' // URL for the student's dashboard
    ]);
}
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}