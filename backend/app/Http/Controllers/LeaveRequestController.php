<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;

class LeaveRequestController extends Controller
{
    /**
     * Handle the API request to store a new leave request.
     * This is the method that the Vue.js frontend calls.
     */
    public function storeApi(Request $request)
    {
        Log::info('Received new leave request from API', $request->all());

        // Check for authenticated user before proceeding
        if (!Auth::check()) {
            Log::warning('Unauthorized API request received.');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            // Validate the incoming request data
            $validated = $request->validate([
                'leave_type_id' => 'required|exists:leave_types,id',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'reason' => 'required|string',
                'supporting_documents' => 'nullable|file|mimes:pdf,jpg,png,docx,doc',
                'contact_info' => 'nullable|string',
            ]);

            Log::info('Request data successfully validated.');

            // Handle file upload
            $path = null;
            if ($request->hasFile('supporting_documents')) {
                $path = $request->file('supporting_documents')->store('documents', 'public');
                Log::info('Supporting document uploaded to: ' . $path);
            }

            // Create the new leave request record
            $leaveRequest = LeaveRequest::create([
                'student_id' => Auth::id(), // Get the ID of the currently authenticated user
                'leave_type_id' => $validated['leave_type_id'],
                'from_date' => $validated['from_date'],
                'to_date' => $validated['to_date'],
                'reason' => $validated['reason'],
                'supporting_documents' => $path,
                'contact_info' => $validated['contact_info'],
                'status' => 'pending'
            ]);

            Log::info('Leave request created with ID: ' . $leaveRequest->id);

            // Send the notification to Telegram
            $this->sendTelegramNotification($leaveRequest);
            Log::info('Telegram notification sent successfully for request ' . $leaveRequest->id);

            // Return a success response to the frontend
            return response()->json(['message' => 'Leave request submitted successfully!', 'data' => $leaveRequest], 201);
        } catch (\Exception $e) {
            // Log any errors that occur and return a 500 error response
            Log::error('An error occurred during leave request submission: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Failed to submit leave request.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Sends a new leave request notification to the Telegram group.
     *
     * @param LeaveRequest $leaveRequest
     * @return void
     */
    public function sendTelegramNotification(LeaveRequest $leaveRequest): void
    {
        try {
            $student = $leaveRequest->student;
            $leaveType = $leaveRequest->leaveType->name;

            // Build the message to be sent to Telegram
            $message = sprintf(
                "🚨 *New Leave Request* [ID: %s]\n" .
                "👤 *Student:* %s\n" .
                "📅 *Dates:* %s to %s\n" .
                "🔄 *Type:* %s\n" .
                "📝 *Reason:* %s\n" .
                "� *Contact:* %s\n\n" .
                "Please approve/reject:",
                $leaveRequest->id,
                $student->name,
                $leaveRequest->from_date->format('M d, Y'),
                $leaveRequest->to_date->format('M d, Y'),
                $leaveType,
                $leaveRequest->reason,
                $leaveRequest->contact_info ?? 'Not provided'
            );

            // Send the message with inline keyboard buttons
            $response = Telegram::sendMessage([
                'chat_id' => env('TELEGRAM_GROUP_ID'),
                'text' => $message,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => '✅ Approve', 'callback_data' => "approve_{$leaveRequest->id}"],
                            ['text' => '❌ Reject', 'callback_data' => "reject_{$leaveRequest->id}"],
                        ]
                    ]
                ])
            ]);

            // If a supporting document exists, send it
            $this->sendDocumentIfExists($leaveRequest);

            Log::info('Telegram notification sent', [
                'request_id' => $leaveRequest->id,
                'message_id' => $response->getMessageId()
            ]);

        } catch (\Exception $e) {
            Log::error('Telegram notification failed', [
                'error' => $e->getMessage(),
                'request_id' => $leaveRequest->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Sends the supporting document to Telegram if it exists.
     *
     * @param LeaveRequest $leaveRequest
     * @return void
     */
    private function sendDocumentIfExists(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->supporting_documents && 
            Storage::disk('public')->exists($leaveRequest->supporting_documents)) {
            
            // Construct the full path to the file using storage_path()
            $filePath = storage_path('app/public/' . $leaveRequest->supporting_documents);
            $inputFile = InputFile::create($filePath, basename($filePath));

            // Send the document
            Telegram::sendDocument([
                'chat_id' => env('TELEGRAM_GROUP_ID'),
                'document' => $inputFile,
                'caption' => 'Supporting document for leave request #' . $leaveRequest->id
            ]);
        }
    }
}
