<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\User;
use App\Mail\LeaveApprovedNotification; 
use App\Mail\LeaveRejectedNotification; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramController extends Controller
{
    /**
     * Handle incoming webhook requests from Telegram.
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Received a new Telegram webhook update.', ['request_data' => $request->all()]);
        
        try {
            $update = Telegram::commandsHandler(true);

            if ($update->isType('callback_query')) {
                return $this->handleCallbackQuery($update);
            }

            if ($update->isType('message')) {
                return $this->handleMessage($update->getMessage());
            }

            return response()->json(['status' => 'ignored']);

        } catch (\Exception $e) {
            Log::error('An error occurred during Telegram webhook handling: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handleCallbackQuery(Update $update)
    {
        $callbackQuery = $update->getCallbackQuery();
        $data = $callbackQuery->getData();
        $message = $callbackQuery->getMessage();
        $user = $callbackQuery->getFrom();

        Log::info('Telegram callback received', [
            'user' => $user->getUsername(),
            'data' => $data
        ]);

        if (preg_match('/^(approve|reject)_(\d+)$/', $data, $matches)) {
            $action = $matches[1];
            $requestId = $matches[2];
            
            return $this->processLeaveRequestAction($requestId, $action, $message, $user);
        }

        return response()->json(['status' => 'invalid_command']);
    }

    private function processLeaveRequestAction($requestId, $action, $message, $telegramUser)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($requestId);

            // Get the educator from your database by their Telegram chat ID
            $educator = User::where('telegram_chat_id', $telegramUser->getId())->first();

            // Check if a valid educator was found
            if (!$educator) {
                Telegram::answerCallbackQuery([
                    'callback_query_id' => $message->callbackQuery->getId(),
                    'text' => "❌ You are not authorized to perform this action. Your account may not be linked.",
                    'show_alert' => true,
                ]);
                return response()->json(['status' => 'unauthorized']);
            }
            
            // Check if the request is still pending before updating
            if ($leaveRequest->status !== 'pending') {
                 Telegram::answerCallbackQuery([
                    'callback_query_id' => $message->callbackQuery->getId(),
                    'text' => "This request has already been {$leaveRequest->status}.",
                    'show_alert' => true,
                ]);
                return response()->json(['status' => 'already_processed']);
            }

            $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
            $approverName = $educator->name;

            // Update status in the database
            $leaveRequest->update([
                'status' => $newStatus,
                'approved_by' => $educator->id,
                'approved_at' => now(),
            ]);

            // Send email notification to the student, just like your EducatorController
            if ($newStatus === 'approved') {
                Mail::to($leaveRequest->student->email)->send(new LeaveApprovedNotification($leaveRequest, $approverName));
            } else {
                 $rejectionReason = "Rejected by {$approverName} via Telegram bot.";
                 $leaveRequest->update(['rejection_reason' => $rejectionReason]);
                 Mail::to($leaveRequest->student->email)->send(new LeaveRejectedNotification($leaveRequest, $rejectionReason, $approverName));
            }

            // Create in-app notification
            $notificationType = ($newStatus === 'approved') ? 'leave_approved' : 'leave_rejected';
            $messageText = "Your leave request for {$leaveRequest->leaveType->name} has been {$newStatus} by {$approverName}.";
            
            Notification::create([
                'user_id' => $leaveRequest->student_id,
                'type' => $notificationType,
                'message' => $messageText,
                'data' => json_encode(['leave_request_id' => $leaveRequest->id]),
                'read' => false,
            ]);

            // Edit original Telegram message
            $messageTextToEdit = $message->getText();
            $statusEmoji = ($newStatus === 'approved') ? '🟢' : '🔴';
            
            Telegram::editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'message_id' => $message->getMessageId(),
                'text' => $messageTextToEdit . "\n\n{$statusEmoji} *Status:* {$newStatus} by {$approverName}",
                'parse_mode' => 'Markdown',
                'reply_markup' => null, // This removes the inline keyboard
            ]);

            // Answer the callback query to stop the button's loading animation
            Telegram::answerCallbackQuery([
                'callback_query_id' => $message->callbackQuery->getId(),
                'text' => "Leave request {$newStatus}d.",
                'show_alert' => false,
            ]);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Failed to process leave request action', [
                'error' => $e->getMessage(),
                'request_id' => $requestId,
                'trace' => $e->getTraceAsString(),
            ]);

            Telegram::sendMessage([
                'chat_id' => $message->getChat()->getId(),
                'text' => "❌ An error occurred while processing the request: " . $e->getMessage()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }
    
    private function handleMessage($message)
    {
        $text = $message->getText();
        $chatId = $message->getChat()->getId();
        
        // Handle the /link command
        if (str_starts_with(strtolower($text), '/link')) {
            $parts = explode(' ', $text);
            $email = $parts[1] ?? null;

            if (!$email) {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '❌ Please provide your email address. Usage: /link your.email@example.com',
                    'parse_mode' => 'Markdown'
                ]);
                return response()->json(['status' => 'missing_email']);
            }

            Log::info("Attempting to link account with email: {$email}");
            $user = User::where('email', $email)->first();
            
            if ($user) {
                Log::info("User found. Linking Telegram ID: {$chatId}");
                $user->telegram_chat_id = $chatId;
                $user->save();

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '✅ Your account has been linked successfully!',
                    'parse_mode' => 'Markdown'
                ]);
            } else {
                 Log::warning("User not found for email: {$email}");
                 Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ We could not find a user account with that email address. Please check and try again.",
                    'parse_mode' => 'Markdown'
                ]);
            }
        } else if (strtolower($text) === '/start') {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Welcome to the PNC LMS Bot! Please use the /link command to connect your account. Example: /link your.email@example.com',
                'parse_mode' => 'Markdown'
            ]);
        }

        return response()->json(['status' => 'message_handled']);
    }

    public function sendLeaveRequestNotification(LeaveRequest $leaveRequest, string $approverTelegramChatId)
    {
        // 1. Prepare the message text
        $messageText = "📢 New Leave Request\n\n";
        $messageText .= "*Student:* {$leaveRequest->student->name}\n";
        $messageText .= "*Leave Type:* {$leaveRequest->leaveType->name}\n";
        $messageText .= "*From:* {$leaveRequest->from_date->format('Y-m-d')}\n";
        $messageText .= "*To:* {$leaveRequest->to_date->format('Y-m-d')}\n";
        $messageText .= "*Reason:* {$leaveRequest->reason}\n";

        // 2. Prepare the inline keyboard buttons
        $inlineKeyboard = [
            [
                'text' => '✅ Approve',
                'callback_data' => 'approve_' . $leaveRequest->id,
            ],
            [
                'text' => '❌ Reject',
                'callback_data' => 'reject_' . $leaveRequest->id,
            ],
        ];

        // 3. Send the message with the inline keyboard
        try {
            // Check if the chat ID is for a private chat (positive number) or a group (negative number)
            $chatType = ($approverTelegramChatId > 0) ? 'private' : 'group';
            
            Log::info("Attempting to send notification to {$chatType} chat with ID: {$approverTelegramChatId}");
            
            Telegram::sendMessage([
                'chat_id' => $approverTelegramChatId,
                'text' => $messageText,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => [$inlineKeyboard]]),
            ]);
            Log::info("Leave request notification sent to Telegram chat ID: {$approverTelegramChatId}");
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification.', [
                'error' => $e->getMessage(),
                'chat_id' => $approverTelegramChatId,
                'leave_request_id' => $leaveRequest->id,
            ]);
        }
    }
}
