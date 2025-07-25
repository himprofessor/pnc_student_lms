<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\StudentLeaveController;
use App\Http\Controllers\API\AdminEducatorController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\LeaveTypeController;
// Public Routes
Route::post('/register/student', [AuthController::class, 'registerStudent']);
Route::post('/register/teacher', [AuthController::class, 'registerTeacher']);
Route::post('/login', [AuthController::class, 'login']);


// Admin Login Route
Route::post('/admin/login', [LoginController::class, 'login']);

// Test route to verify API is working
 
//Type of leave 
Route::get('/leave-types', [LeaveTypeController::class, 'index']);
// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Profile Routes (Available to ALL authenticated users)
    Route::get('/user', [UserController::class, 'getCurrentUser']);        // GET profile
    Route::put('/user/profile', [UserController::class, 'updateProfile']); // UPDATE profile
    
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::post('/user/upload-image', [UserController::class, 'uploadProfileImage']);
    Route::delete('/user/delete-image', [UserController::class, 'deleteProfileImage']);

    // Admin Routes
  // Admin Protected Routes
// -----------------------
Route::middleware(['auth:sanctum', 'role:1'])->group(function () {
    Route::get('/educators', [AdminEducatorController::class, 'index']);
    Route::post('/educators', [AdminEducatorController::class, 'store']);
    
    // You can add more routes that require the same role here
    Route::get('/educators/{id}', [AdminEducatorController::class, 'show']);
    Route::put('/educators/{id}', [AdminEducatorController::class, 'update']);
    Route::delete('/educators/{id}', [AdminEducatorController::class, 'destroy']);
});

    // Teacher Routes
  // Teacher Routes
Route::middleware(['auth:sanctum'])->get('/teacher-dashboard', function (Request $request) {
    $user = $request->user();

    if (!$user->canAccessTeacherDashboard()) {
        return response()->json(['error' => 'Unauthorized access'], 403);
    }

    return response()->json([
        'message' => 'Welcome, ' . $user->name . '! You are logged in as a teacher.'
    ]);
});
// 🆕 New Route — Get authenticated teacher info
Route::middleware(['auth:sanctum'])->get('/teacher', [TeacherController::class, 'getAuthenticatedTeacher']);


    // Student Routes
    Route::middleware('role:3')->group(function () {
        Route::post('/student/request-leave', [StudentLeaveController::class, 'requestLeave']);
        Route::get('/student/my-leaves', [StudentLeaveController::class, 'myLeaves']);
        Route::get('/student/leave-history', [StudentLeaveController::class, 'leaveHistory']);
        Route::get('/student/dashboard', [StudentLeaveController::class, 'dashboard']);
        Route::get('/student/leave-request/{id}', [StudentLeaveController::class, 'leaveRequestDetails']);
        Route::put('/student/leave-request/{id}', [StudentLeaveController::class, 'updateLeaveRequest']);
        Route::delete('/student/leave-request/{id}', [StudentLeaveController::class, 'deleteLeaveRequest']);
    });
});
