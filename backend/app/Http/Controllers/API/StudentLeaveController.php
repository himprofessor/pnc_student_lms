<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLeaveController extends Controller
{
    // Submit a new leave request
       public function requestLeave(Request $request)
{
    $request->validate([
        'reason' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
    ]);

    $leave = LeaveRequest::create([
        'student_id' => Auth::id(), // Ensure this user is a student
        'reason' => $request->reason,
        'from_date' => $request->from_date,
        'to_date' => $request->to_date,
        'status' => 'pending',
    ]);

    return response()->json([
        'message' => 'Leave request submitted successfully.',
        'data' => $leave,
    ], 201);
}

        // View logged-in student's leave requests
        public function myLeaves()
        {
            $leaves = LeaveRequest::where('student_id', Auth::id())->get();

            return response()->json(['leaves' => $leaves]);
        }

    // Dashboard summary
    public function dashboard()
    {
        $total = LeaveRequest::where('student_id', Auth::id())->count();
        $approved = LeaveRequest::where('student_id', Auth::id())->where('status', 'approved')->count();
        $pending = LeaveRequest::where('student_id', Auth::id())->where('status', 'pending')->count();

        return response()->json([
            'total_requests' => $total,
            'approved' => $approved,
            'pending' => $pending,
        ]);
    }
}