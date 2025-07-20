<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\StudentLeaveController;
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

    // Admin Routes
    Route::middleware('role:admin')->get('/admin-area', fn() => 'Admin Access');

    // Teacher Routes
    Route::middleware('role:teacher')->group(function () {
        Route::get('/teacher-area', fn() => 'Teacher Access');
        Route::get('/leave-requests', [TeacherController::class, 'viewLeaveRequests']);
        Route::post('/leave-requests/{id}/approve', [TeacherController::class, 'approve']);
        Route::post('/leave-requests/{id}/reject', [TeacherController::class, 'reject']);
        Route::get('/students', [TeacherController::class, 'studentsList']);
    });

    // Student Routes
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