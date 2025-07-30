<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducatorController extends Controller
{

    // Educator: View all student leave requests
public function getAllLeaveRequests()
{
    $leaveRequests = LeaveRequest::with(['student', 'leaveType'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($leaveRequests->map(function ($leave) {
        return [
            'id' => $leave->id,
            'student' => $leave->student->name ?? 'Unknown',
            'student_id' => $leave->student_id,
            'leave_type' => $leave->leaveType->name ?? 'N/A',
            'duration' => $leave->from_date . ' to ' . $leave->to_date,
            'status' => ucfirst($leave->status),
            'submitted' => $leave->created_at->format('Y-m-d'),

            // Add profile image URL - use the img_url accessor or img field
            'profile_image' => $leave->student && $leave->student->img_url
                ? $leave->student->img_url
                : ($leave->student && $leave->student->img 
                    ? url('storage/' . $leave->student->img)
                    : url('storage/profile-images/default.png')), // fallback default image
        ];
    }));
}


// In StudentLeaveController

// Approve leave request as educator
public function approveLeaveRequest(Request $request, $id)
{
    $leave = LeaveRequest::findOrFail($id);
    $leave->update([
        'status' => 'approved',
        'approved_by' => Auth::id(),
        'rejection_reason' => null,
    ]);

    return response()->json([
        'message' => 'Leave request approved',
        'data' => $leave->load('student', 'leaveType', 'approver')
    ]);
}

// Reject leave request with reason
public function rejectLeaveRequest(Request $request, $id)
{
    $request->validate(['rejection_reason' => 'required|string|max:255']);

    $leave = LeaveRequest::findOrFail($id);
    $leave->update([
        'status' => 'rejected',
        'approved_by' => Auth::id(),
        'rejection_reason' => $request->rejection_reason,
    ]);

    return response()->json([
        'message' => 'Leave request rejected',
        'data' => $leave->load('student', 'leaveType', 'approver')
    ]);
}

// Detail view
public function getLeaveRequest($id)
{
    $leave = LeaveRequest::with(['student', 'leaveType', 'approver'])
        ->findOrFail($id);

    return response()->json([
        'leave_request' => [
            'id' => $leave->id,
            'student' => $leave->student->name,
            'leave_type' => $leave->leaveType->name,
            'from_date' => $leave->from_date,
            'to_date' => $leave->to_date,
            'duration' => $leave->from_date . ' to ' . $leave->to_date,
            'reason' => $leave->reason,
            'contact_info' => $leave->contact_info,
            'supporting_documents' => $leave->supporting_documents,
            'status' => ucfirst($leave->status),
            'submitted' => $leave->created_at->format('Y-m-d'),
            'approved_by' => optional($leave->approver)->name,
            'rejection_reason' => $leave->rejection_reason,
            // Add profile image for the detail view as well
            'profile_image' => $leave->student && $leave->student->img_url
                ? $leave->student->img_url
                : ($leave->student && $leave->student->img 
                    ? url('storage/' . $leave->student->img)
                    : url('storage/profile-images/default.png')),
        ]
    ]);
}

}
