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
            'leave_type_id' => 'required|exists:leave_types,id', // Validate the leave type
            'reason' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'contact_info' => 'nullable|string',
            'supporting_documents' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        // Handle file upload
        $path = null;
        if ($request->hasFile('supporting_documents')) {
            $path = $request->file('supporting_documents')?->store('supporting_documents', 'public');

        }

        $leave = LeaveRequest::create([
            'student_id' => Auth::id(),
            'leave_type_id' => $request->leave_type_id,
            'reason' => $request->reason,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'contact_info' => $request->contact_info,
            'supporting_documents' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Leave request submitted successfully.',
            'data' => [
                'id' => $leave->id,
                'student_id' => $leave->student_id,
                'leave_type_id' => $leave->leave_type_id,
                'reason' => $leave->reason,
                'from_date' => $leave->from_date,
                'to_date' => $leave->to_date,
                'contact_info' => $leave->contact_info,
                'status' => $leave->status,
                'created_at' => $leave->created_at,
                'updated_at' => $leave->updated_at,
                'supporting_documents' => $leave->supporting_documents,
               'document_url' => $leave->supporting_documents
                ? url('storage/' . $leave->supporting_documents)
                : null,

            ]
        ], 201);
        
    }

    // View logged-in student's leave requests
    public function myLeaves()
    {
        $leaves = LeaveRequest::with('leaveType')->where('student_id', Auth::id())->get();

        // Transform the response to include leave type name instead of ID
        $leaves = $leaves->map(function ($leave) {
            return [
                'id' => $leave->id,
                'reason' => $leave->reason,
                'from_date' => $leave->from_date,
                'to_date' => $leave->to_date,
                'contact_info' => $leave->contact_info,
                'supporting_documents' => $leave->supporting_documents,
                'document_url' => $leave->supporting_documents
                    ? url('storage/' . $leave->supporting_documents)
                    : null,
                'status' => $leave->status,
                'leave_type' => $leave->leaveType->name, // Get the leave type name
            ];
        });

        return response()->json(['leaves' => $leaves]);
    }

    // Dashboard summary
    public function dashboard()
    {
        $total = LeaveRequest::where('student_id', Auth::id())->count();
        $approved = LeaveRequest::where('student_id', Auth::id())->where('status', 'approved')->count();
        $pending = LeaveRequest::where('student_id', Auth::id())->where('status', 'pending')->count();
        $rejected = LeaveRequest::where('student_id', Auth::id())->where('status', 'rejected')->count();

        $approvalRate = $total > 0 ? ($approved / $total) * 100 : 0;

        return response()->json([
            'total_requests' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
            'approval_rate' => $approvalRate,
        ]);
    }

    // View details of a specific leave request
    public function leaveRequestDetails($id)
    {
        $leaveRequest = LeaveRequest::with('leaveType') // Eager load leave type
                                    ->where('id', $id)
                                    ->where('student_id', Auth::id())
                                    ->first();

        if (!$leaveRequest) {
            return response()->json(['error' => 'Leave request not found'], 404);
        }

        return response()->json(['leave_request' => [
            'id' => $leaveRequest->id,
            'reason' => $leaveRequest->reason,
            'from_date' => $leaveRequest->from_date,
            'to_date' => $leaveRequest->to_date,
            'contact_info' => $leaveRequest->contact_info,
            'supporting_documents' => $leaveRequest->supporting_documents,
            'document_url' => $leaveRequest->supporting_documents
            ? url('storage/' . $leaveRequest->supporting_documents)
            : null,

            'status' => $leaveRequest->status,
            'leave_type' => $leaveRequest->leaveType->name, // Get the leave type name
        ]]);
    }

    // Update a leave request
    public function updateLeaveRequest(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::where('id', $id)
                                    ->where('student_id', Auth::id())
                                    ->first();

        if (!$leaveRequest) {
            return response()->json(['error' => 'Leave request not found'], 404);
        }

        $request->validate([
            'reason' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'contact_info' => 'nullable|string',
            'supporting_documents' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);
       
        // Handle file upload if a new document is provided
        $documentPath = $leaveRequest->supporting_documents; // Keep the existing document
        if ($request->hasFile('supporting_documents')) {
            // Delete the old document if necessary (optional)
            // Storage::delete($documentPath);
            $documentPath = $request->file('supporting_documents')->store('supporting_documents');
        }

        // Update the leave request
        $leaveRequest->update([
            'reason' => $request->reason,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'contact_info' => $request->contact_info,
            'supporting_documents' => $documentPath,
            // Status should not be changed if it's already approved or rejected
        ]);

        return response()->json(['message' => 'Leave request updated successfully', 'data' => $leaveRequest]);
    }
    // Cancel a leave request
    public function deleteLeaveRequest($id)
    {
        // Find the leave request by ID and ensure it belongs to the authenticated student
        $leaveRequest = LeaveRequest::where('id', $id)
                                    ->where('student_id', Auth::id())
                                    ->first();

        // Return an error response if the leave request is not found
        if (!$leaveRequest) {
            return response()->json(['error' => 'Leave request not found'], 404);
        }

        // Check if the status is 'pending'
        if ($leaveRequest->status !== 'pending') {
            return response()->json(['error' => 'Only pending requests can be deleted'], 403);
        }

        // Delete the leave request
        $leaveRequest->delete();

        // Return a success response
        return response()->json(['message' => 'Leave request deleted successfully']);
    }
}   