<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;

class TelegramController extends Controller
{
    // List of allowed admin Telegram user IDs
    private $allowedAdmins = [
        123456789, // replace with real Telegram user ID of admin 1
        987654321, // replace with real Telegram user ID of admin 2
    ];

    public function handleWebhook(Request $request)
    {
        $update = Telegram::commandsHandler(true);
        
        // Handle button callbacks
        if ($update->isType('callback_query')) {
            return $this->handleCallbackQuery($update);
        }

        // Handle regular messages (approve/reject commands)
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

        // Extract request ID and action
        if (preg_match('/^(approve|reject)_(\d+)$/', $data, $matches)) {
            $action = $matches[1];
            $requestId = $matches[2];
            
            return $this->processLeaveRequestAction($requestId, $action, $message, $user);
        }

        return response()->json(['status' => 'invalid_command']);
    }

    private function processLeaveRequestAction($requestId, $action, $message, $fromUser)
    {
        try {
            // Check if user is allowed
            if (!in_array($fromUser->getId(), $this->allowedAdmins)) {
                Telegram::sendMessage([
                    'chat_id' => $message->getChat()->getId(),
                    'text' => "⛔ You are not authorized to approve/reject requests."
                ]);
                return response()->json(['status' => 'unauthorized']);
            }

            $leaveRequest = LeaveRequest::findOrFail($requestId);
            $approverName = $fromUser->getFirstName();

            // Update status
            $leaveRequest->update([
                'status' => $action === 'approve' ? 'approved' : 'rejected',
                'approved_by' => $fromUser->getId(),
                'approved_at' => now()
            ]);

            // Edit original message
            Telegram::editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'message_id' => $message->getMessageId(),
                'text' => $message->getText() . "\n\n🟢 *Status:* {$action}d by {$approverName}",
                'parse_mode' => 'Markdown'
            ]);

            // Notify student
            $this->notifyStudent($leaveRequest, $action);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Failed to process leave request action', [
                'error' => $e->getMessage(),
                'request_id' => $requestId
            ]);

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

        // If student has Telegram chat_id stored
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
        $text = trim($message->getText());
        $chatId = $message->getChat()->getId();
        $from = $message->getFrom();

        // Start command
        if (strtolower($text) === '/start') {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Welcome to PNC LMS Bot! Use commands: approve <id> or reject <id> to manage leave requests.',
                'parse_mode' => 'Markdown'
            ]);
            return response()->json(['status' => 'message_handled']);
        }

        // Match approve/reject command from message
        if (preg_match('/^(approve|reject)\s+(\d+)$/i', $text, $matches)) {
            $action = strtolower($matches[1]);
            $requestId = (int) $matches[2];

            // Process request
            return $this->processLeaveRequestAction(
                $requestId,
                $action,
                $message,
                $from
            );
        }

        // Unknown command
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Unknown command. Use: approve <id> or reject <id>"
        ]);

        return response()->json(['status' => 'message_handled']);
    }
}
