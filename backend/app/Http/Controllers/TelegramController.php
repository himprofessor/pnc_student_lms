<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Notification; // <--- ADDED: Import the new Notification model
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $update = Telegram::commandsHandler(true);
        
        if ($update->isType('callback_query')) {
            return $this->handleCallbackQuery($update);
        }

        if ($update->isType('message')) {
            return $this->handleMessage($update->getMessage());
        }

        return response()->json(['status' => 'ignored']);
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
            
            return $this->processLeaveRequestAction($requestId, $action, $message);
        }

        return response()->json(['status' => 'invalid_command']);
    }

    private function processLeaveRequestAction($requestId, $action, $message)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($requestId);
            $approverName = $message->getFrom()->getFirstName();
            $newStatus = ($action === 'approve') ? 'approved' : 'rejected';

            // Update status
            $leaveRequest->update([
                'status' => $newStatus,
                'approved_by' => $message->getFrom()->getId(),
                'approved_at' => now()
            ]);
            
            // <--- ADDED: Create a notification for the student
            $notificationType = ($newStatus === 'approved') ? 'leave_approved' : 'leave_rejected';
            $messageText = "Your leave request for '" . $leaveRequest->leaveType->name . "' has been " . $newStatus . ".";
            
            Notification::create([
                'user_id' => $leaveRequest->student_id,
                'type' => $notificationType,
                'message' => $messageText,
                'data' => json_encode(['leave_request_id' => $leaveRequest->id]),
                'read' => false,
            ]);
            
            // <--- END ADDITION

            // Edit original message
            $messageTextToEdit = $message->getText();
            $statusEmoji = ($action === 'approve') ? '🟢' : '🔴';
            
            Telegram::editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'message_id' => $message->getMessageId(),
                'text' => $messageTextToEdit . "\n\n{$statusEmoji} *Status:* {$action}d by {$approverName}",
                'parse_mode' => 'Markdown',
                'reply_markup' => null, // This removes the inline keyboard
            ]);

            // Answer the callback query to stop the button's loading animation
            Telegram::answerCallbackQuery([
                'callback_query_id' => $message->callbackQuery->getId(),
                'text' => "Leave request {$newStatus}d.",
                'show_alert' => false,
            ]);

            // Send confirmation to user (optional, can be done via new notification system)
            // $this->notifyStudent($leaveRequest, $action);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Failed to process leave request action', [
                'error' => $e->getMessage(),
                'request_id' => $requestId
            ]);

            // Send a failure message to the teacher's bot chat
            Telegram::sendMessage([
                'chat_id' => $message->getChat()->getId(),
                'text' => "❌ Failed to process request: " . $e->getMessage()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    private function notifyStudent(LeaveRequest $leaveRequest, $action)
    {
        $status = $action === 'approve' ? 'approved ✅' : 'rejected ❌';
        $message = "Your leave request #{$leaveRequest->id} has been {$status}";

        if ($leaveRequest->user->telegram_chat_id) {
            Telegram::sendMessage([
                'chat_id' => $leaveRequest->user->telegram_chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
        }
    }

    private function handleMessage($message)
    {
        $text = $message->getText();
        $chatId = $message->getChat()->getId();

        if (strtolower($text) === '/start') {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Welcome to PNC LMS Bot!',
                'parse_mode' => 'Markdown'
            ]);
        }

        return response()->json(['status' => 'message_handled']);
    }
}
