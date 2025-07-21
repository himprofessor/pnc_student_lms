<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
   public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // Log incoming credentials
    \Log::info('Login attempt:', $credentials);

    if (Auth::attempt($credentials)) {
        // Authentication passed
        $user = Auth::user();
        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
        ]);
    }

    // Log failed login attempt
    \Log::warning('Login failed for:', $credentials);
    return response()->json(['error' => 'Unauthorized'], 401);
}
}