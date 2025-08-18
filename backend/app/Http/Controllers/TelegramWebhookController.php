<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Handle incoming webhook requests from Telegram.
     */
    public function handle(Request $request)
    {
        Log::info('Received a new Telegram webhook update.', ['request_data' => $request->all()]);
        
        try {
            // Get the entire update object from the request
            $update = new Update($request->all());

            // Check if the update is a message (for commands like /start)
            if ($message = $update->getMessage()) {
                $text = $message->getText();
                $chatId = $message->getChat()->getId();
                
                // Handle the /start or /link command
                if ($text === '/start' || $text === '/link') {
                    $link = route('link.telegram', ['chat_id' => $chatId]);
                    $responseMessage = "Hello! To approve or reject leave requests, you must first link your Telegram account to your educator account.
                    \nTo do so, please log into the dashboard and click the following link from the device where you have Telegram installed:
                    \n{$link}
                    \n\n*This is a one-time setup.*";

                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $responseMessage,
                        'parse_mode' => 'Markdown',
                    ]);
                    Log::info("Sent account linking instructions to chat ID: {$chatId}");
                    return response()->json(['status' => 'ok']);
                }
            }

            // Check if the update is a callback query (a button click)
            if ($callbackQuery = $update->getCallbackQuery()) {
                $data = $callbackQuery->getData();
                $message = $callbackQuery->getMessage();
                $chatId = $message->getChat()->getId();
                $messageId = $message->getMessageId();
                $user = $callbackQuery->getFrom();

                Log::info("Callback query received from user {$user->getFirstName()} ({$user->getId()}) with data: {$data}");

                if (!preg_match('/^(approve|reject)_(\d+)$/', $data, $matches)) {
                    Log::warning("Invalid callback data format: {$data}");
                    Telegram::answerCallbackQuery([
                        'callback_query_id' => $callbackQuery->getId(),
                        'text' => 'Invalid action. Please try again.',
                        'show_alert' => true,
                    ]);
                    return response()->json(['status' => 'invalid_callback_data']);
                }

                $action = $matches[1];
                $requestId = $matches[2];
                
                $leaveRequest = LeaveRequest::with(['student', 'leaveType'])->find($requestId);

                if (!$leaveRequest) {
                    Telegram::answerCallbackQuery([
                        'callback_query_id' => $callbackQuery->getId(),
                        'text' => 'Leave request not found!',
                        'show_alert' => true,
                    ]);
                    Log::warning("Leave request with ID {$requestId} not found.");
                    return response()->json(['status' => 'error', 'message' => 'Leave request not found.'], 404);
                }

                $currentStatus = $leaveRequest->status;

                if ($currentStatus === 'pending') {
                    $newStatus = ($action === 'approve') ? 'approved' : 'rejected';

                    // Check if the Telegram user is linked to an account
                    $educator = User::where('telegram_chat_id', $user->getId())->first();

                    if (!$educator) {
                        Telegram::answerCallbackQuery([
                            'callback_query_id' => $callbackQuery->getId(),
                            'text' => '❌ You are not authorized to perform this action. Your account may not be linked.',
                            'show_alert' => true,
                        ]);
                        Log::warning("Unauthorized action attempt by Telegram user: {$user->getId()}");
                        return response()->json(['status' => 'unauthorized']);
                    }

                    $leaveRequest->update([
                        'status' => $newStatus,
                        'approved_by' => $educator->id,
                        'approved_at' => now(),
                    ]);
                    
                    // Create a notification for the student
                    $notificationType = ($newStatus === 'approved') ? 'leave_approved' : 'leave_rejected';
                    $messageText = "Your leave request for '" . $leaveRequest->leaveType->name . "' has been " . $newStatus . ".";
                    
                    Notification::create([
                        'user_id' => $leaveRequest->student_id,
                        'type' => $notificationType,
                        'message' => $messageText,
                        'data' => json_encode(['leave_request_id' => $leaveRequest->id]),
                        'read' => false,
                    ]);

                    $responseText = "You have {$newStatus}d the leave request.";
                } else {
                    $newStatus = $currentStatus;
                    $responseText = "This request has already been {$newStatus}.";
                }

                // Edit the original message to show the updated status
                $originalMessageText = $message->getText();
                $updatedMessageText = str_replace(
                    "Please approve/reject:",
                    "Status: *" . ucfirst($newStatus) . "*",
                    $originalMessageText
                );

                try {
                    Telegram::editMessageText([
                        'chat_id' => $chatId,
                        'message_id' => $messageId,
                        'text' => $updatedMessageText,
                        'parse_mode' => 'Markdown',
                        'reply_markup' => null, // This removes the keyboard buttons
                    ]);
                    Log::info("Successfully edited message for request ID {$requestId} in Telegram.");
                } catch (\Exception $e) {
                    Log::error('Failed to edit Telegram message: ' . $e->getMessage(), [
                        'chat_id' => $chatId,
                        'message_id' => $messageId,
                        'exception' => $e
                    ]);
                }

                try {
                    Telegram::answerCallbackQuery([
                        'callback_query_id' => $callbackQuery->getId(),
                        'text' => $responseText,
                        'show_alert' => false,
                    ]);
                    Log::info("Successfully answered callback query for request ID {$requestId}.");
                } catch (\Exception $e) {
                    Log::error('Failed to answer callback query: ' . $e->getMessage(), [
                        'exception' => $e
                    ]);
                }
            } else {
                Log::info('Received a non-callback Telegram update.');
            }
        } catch (\Exception $e) {
            Log::error('An error occurred during Telegram webhook handling: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['status' => 'ok']);
    }
}
