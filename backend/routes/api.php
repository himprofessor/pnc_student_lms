<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\EducatorController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\StudentLeaveController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\LeaveTypeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\TelegramController;

// Public Routes
Route::post('/register/student', [AuthController::class, 'registerStudent']);
Route::post('/register/teacher', [AuthController::class, 'registerTeacher']);
Route::post('/login', [AuthController::class, 'login']);


// Admin Login Route
Route::post('/admin/login', [LoginController::class, 'login']);

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working',
        'time' => now(),
        'status' => 'success'
    ]);
});

//Type of leave 
Route::get('/leave-types', [LeaveTypeController::class, 'index']);
// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    
    // This route is accessible to authenticated users with the 'educator' role
    Route::get('/educator/leave-requests', [EducatorController::class, 'getAllLeaveRequests']);
    Route::get('/educator/leave-request/{id}', [EducatorController::class, 'getLeaveRequest']);
    Route::post('/educator/leave-request/{id}/approve', [EducatorController::class, 'approveLeaveRequest']);
    Route::post('/educator/leave-request/{id}/reject', [EducatorController::class, 'rejectLeaveRequest']);

    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Profile Routes (Available to ALL authenticated users)
    Route::get('/user', [UserController::class, 'getCurrentUser']);        // GET profile
    Route::put('/user/profile', [UserController::class, 'updateProfile']); // UPDATE profile
    
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::post('/user/upload-image', [UserController::class, 'uploadProfileImage']);
    Route::delete('/user/delete-image', [UserController::class, 'deleteProfileImage']);

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin-area', fn() => 'Admin Access');
        
        // User management routes for admin
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete']);
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

Route::middleware('auth:api')->group(function () {
    // Student submits leave request (API endpoint)
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    
    // Educator lists pending requests (API endpoint)
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
});

// routes/api.php
Route::post('/telegram/webhook', [TelegramController::class, 'handleWebhook']);

// routes/api.php
Route::post('/student/request-leave', [LeaveRequestController::class, 'storeApi'])->middleware('auth:sanctum');