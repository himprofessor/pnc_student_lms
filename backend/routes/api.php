<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
<<<<<<< HEAD
use App\Http\Controllers\API\UserController;
=======
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\StudentLeaveController;
>>>>>>> ce40a5cc316baf10b7fb2760e756e62a55540d92

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

<<<<<<< HEAD
  // User CRUD Routes
  Route::apiResource('users', UserController::class);
  Route::get('/user', function (Request $request) {
    return $request->user();
});

  
=======
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
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/student/request-leave', [StudentLeaveController::class, 'requestLeave']);
        Route::get('/student/my-leaves', [StudentLeaveController::class, 'myLeaves']);
        Route::get('/student/leave-history', [StudentLeaveController::class, 'leaveHistory']);
        Route::get('/student/dashboard', [StudentLeaveController::class, 'dashboard']);
        Route::get('/student/leave-request/{id}', [StudentLeaveController::class, 'leaveRequestDetails']);
        Route::put('/student/leave-request/{id}', [StudentLeaveController::class, 'updateLeaveRequest']);
        Route::delete('/student/leave-request/{id}', [StudentLeaveController::class, 'deleteLeaveRequest']);
    });

});
>>>>>>> ce40a5cc316baf10b7fb2760e756e62a55540d92
