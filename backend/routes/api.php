<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\StudentLeaveController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\LeaveTypeController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register/student', [AuthController::class, 'registerStudent']);
Route::post('/register/teacher', [AuthController::class, 'registerTeacher']);
Route::post('/login', [AuthController::class, 'login']);


// Admin Login Route
Route::post('/admin/login', [LoginController::class, 'login']);

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working',
        'time' => now(),
        'status' => 'success'
    ]);
});

// Leave types (public)
Route::get('/leave-types', [LeaveTypeController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Protected Routes (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // ✅ Common User Profile Routes
    Route::get('/profile', [UserController::class, 'viewProfile']);  
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::put('/profile/password', [UserController::class, 'updatePassword']);
    Route::post('/profile/upload-image', [UserController::class, 'uploadProfileImage']);
    Route::delete('/profile/delete-image', [UserController::class, 'deleteProfileImage']);

    // ✅ Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete']);
    });

    /*
    |--------------------------------------------------------------------------
    | Teacher Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:teacher')->group(function () {
        Route::get('/leave-requests', [TeacherController::class, 'viewLeaveRequests']);
        Route::post('/leave-requests/{id}/approve', [TeacherController::class, 'approve']);
        Route::post('/leave-requests/{id}/reject', [TeacherController::class, 'reject']);
        Route::get('/students', [TeacherController::class, 'studentsList']);
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:student')->group(function () {
        Route::post('/student/request-leave', [StudentLeaveController::class, 'requestLeave']);
        Route::get('/student/my-leaves', [StudentLeaveController::class, 'myLeaves']);
        Route::get('/student/leave-history', [StudentLeaveController::class, 'leaveHistory']);
        Route::get('/student/dashboard', [StudentLeaveController::class, 'dashboard']);
        Route::get('/student/leave-request/{id}', [StudentLeaveController::class, 'leaveRequestDetails']);
        Route::put('/student/leave-request/{id}', [StudentLeaveController::class, 'updateLeaveRequest']);
        Route::delete('/student/leave-request/{id}', [StudentLeaveController::class, 'deleteLeaveRequest']);
    });
});
