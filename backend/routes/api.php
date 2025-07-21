<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\StudentLeaveController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\LoginController;

// Public Routes
Route::post('/register/student', [AuthController::class, 'registerStudent']);
Route::post('/register/teacher', [AuthController::class, 'registerTeacher']);
Route::post('/login', [AuthController::class, 'login']);

// Admin Login Route
Route::post('/admin/login', [LoginController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Profile Routes - ADD THESE LINES
    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->load('role'));
    });
    Route::put('/user/profile', [UserController::class, 'update']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin-area', fn() => 'Admin Access');
        Route::apiResource('users', UserController::class);
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete']);
    });

    // Teacher Routes
    Route::middleware('role:teacher')->group(function () {
        Route::get('/teacher-area', fn() => 'Teacher Access');
        Route::get('/leave-requests', [TeacherController::class, 'viewLeaveRequests']);
        Route::post('/leave-requests/{id}/approve', [TeacherController::class, 'approve']);
        Route::post('/leave-requests/{id}/reject', [TeacherController::class, 'reject']);
        Route::get('/students', [TeacherController::class, 'studentsList']);
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
