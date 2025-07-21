<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\User;

class TeacherController extends Controller
{
    public function viewLeaveRequests()
    {
        $leaveRequests = LeaveRequest::with('student')->where('status', 'pending')->get();
        return response()->json($leaveRequests);
    }

    public function approve($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->status = 'approved';
        $leave->save();

        return response()->json(['message' => 'Leave approved']);
    }

   public function reject(Request $request, $id)
{
    $request->validate([
        'comment' => 'required|string'
    ]);

    $leave = LeaveRequest::findOrFail($id);
    $leave->status = 'rejected';
    $leave->comment = $request->comment; // ➤ Add rejection comment
    $leave->save();

    return response()->json(['message' => 'Leave rejected with comment']);
}


    public function studentsList()
    {
        $students = User::whereHas('role', function ($q) {
            $q->where('name', 'student');
        })->get();

        return response()->json($students);
    }
}
