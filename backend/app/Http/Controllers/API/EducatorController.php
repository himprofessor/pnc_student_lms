<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Educator; // Make sure to import the Educator model
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Mail\LeaveApprovedNotification;
use App\Mail\LeaveRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Imports\StudentsImport; // Import the new import class
use Maatwebsite\Excel\Facades\Excel; // Import the Excel facade
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Illuminate\Support\Facades\Log;
class EducatorController extends Controller
{
    // Educator: View all student leave requests
    public function getAllLeaveRequests()
    {
        $leaveRequests = LeaveRequest::with(['student', 'leaveType', 'approver'])
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
                'approved_by' => optional($leave->approver)->name ?? 'N/A',
                'profile_image' => $leave->student && $leave->student->img_url
                    ? $leave->student->img_url
                    : ($leave->student && $leave->student->img 
                        ? url('storage/' . $leave->student->img)
                        : url('storage/profile-images/default.png')),
            ];
        }));
    }

    // Approve leave request as educator
    public function approveLeaveRequest(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $educator = Auth::user(); // Get the logged-in educator
        $leave->update([
            'status' => 'approved',
            'approved_by' => $educator->id,
            'rejection_reason' => null,
        ]);

        // Send email notification with educator's name
        Mail::to($leave->student->email)->send(new LeaveApprovedNotification($leave, $educator->name));

        // Create in-app notification with educator's name
        Notification::create([
            'user_id' => $leave->student_id,
            'type' => 'leave_approved',
            'message' => "Your leave request for {$leave->leaveType->name} from {$leave->from_date} to {$leave->to_date} has been approved by {$educator->name}.",
            'data' => ['leave_request_id' => $leave->id],
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
        $educator = Auth::user(); // Get the logged-in educator
        $leave->update([
            'status' => 'rejected',
            'approved_by' => $educator->id,
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Send email notification with educator's name
        Mail::to($leave->student->email)->send(new LeaveRejectedNotification($leave, $request->rejection_reason, $educator->name));

        // Create in-app notification with educator's name
        Notification::create([
            'user_id' => $leave->student_id,
            'type' => 'leave_rejected',
            'message' => "Your leave request for {$leave->leaveType->name} from {$leave->from_date} to {$leave->to_date} has been rejected by {$educator->name}. Reason: {$request->rejection_reason}",
            'data' => ['leave_request_id' => $leave->id],
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
                'approved_by' => optional($leave->approver)->name ?? 'N/A',
                'rejection_reason' => $leave->rejection_reason,
                'profile_image' => $leave->student && $leave->student->img_url
                    ? $leave->student->img_url
                    : ($leave->student && $leave->student->img 
                        ? url('storage/' . $leave->student->img)
                        : url('storage/profile-images/default.png')),
            ]
        ]);
    }

    // Educator: Create student account one by one 
    public function createStudentAccount(Request $request)
    {
        try {
            // Instantiate the Educator model to call the method
            $educatorModel = new Educator();
            
            // Call the method on the Educator model instance
            $student = $educatorModel->createStudentAccount($request->all());

            return response()->json([
                'message' => 'Student account created successfully.',
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
   // Import from Excel
    public function __construct()
        {
            $this->middleware('auth:sanctum');
            $this->middleware('role:2')->only('importStudents'); // Assuming role 2 is educator
        }

    public function importStudents(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|mimes:csv,txt'
            ]);

            $file = $request->file('excel_file');
            Log::info('File received', [
                'path' => $file->getPathname(),
                'originalName' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ]);

            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setHeaderOffset(0); // Use first row as headers
            $csv->setDelimiter(','); // Explicitly set comma as delimiter

            $headers = $csv->getHeader();
            Log::info('CSV Headers', ['headers' => $headers]);

            if (count($headers) < 3 || !in_array('Name', $headers) || !in_array('Email', $headers) || !in_array('Password', $headers)) {
                throw new \Exception('Invalid CSV header format. Expected "Name", "Email", "Password".');
            }

            $records = $csv->getRecords();
            $errors = [];

            foreach ($records as $index => $record) {
                $rowNumber = $index + 2; // +2 because header is offset 0, and index starts at 0
                Log::info('Processing row', ['rowNumber' => $rowNumber, 'data' => (array) $record]);

                $name = trim($record['Name'] ?? ''); // Match the exact header case
                $email = trim($record['Email'] ?? '');
                $password = trim($record['Password'] ?? '');

                if (empty($name) || empty($email) || empty($password)) {
                    $errors[] = "Row {$rowNumber}: Name, email, and password are required. Data: " . json_encode((array) $record);
                    continue;
                }

                try {
                    $educator = new Educator();
                    $userData = ['name' => $name, 'password' => $password, 'password_confirmation' => $password];
                    $user = $educator->createStudentAccount($userData);
                    $user->role_id = 3; // Ensure student role
                    $user->save();
                    Log::info('User created', ['email' => $email, 'id' => $user->id]);
                } catch (ValidationException $e) {
                    $errors[] = "Row {$rowNumber}: Validation failed. " . json_encode($e->errors());
                    continue;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: Error creating user. " . $e->getMessage();
                    continue;
                }
            }

            if (!empty($errors)) {
                Log::warning('Import errors', ['errors' => $errors]);
                return response()->json(['message' => 'Import completed with some errors.', 'errors' => $errors], 206);
            }

            return response()->json(['message' => 'Students imported successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Import failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'An error occurred during import.', 'error' => $e->getMessage()], 500);
        }
    }
}