<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $update = Telegram::commandsHandler(true);
        
        // Handle button callbacks
        if ($update->isType('callback_query')) {
            return $this->handleCallbackQuery($update);
        }

        // Handle regular messages (optional)
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
            
            return $this->processLeaveRequestAction($requestId, $action, $message);
        }

        return response()->json(['status' => 'invalid_command']);
    }

    private function processLeaveRequestAction($requestId, $action, $message)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($requestId);
            $approverName = $message->getFrom()->getFirstName();

            // Update status
            $leaveRequest->update([
                'status' => $action === 'approve' ? 'approved' : 'rejected',
                'approved_by' => $message->getFrom()->getId(),
                'approved_at' => now()
            ]);

            // Edit original message
            Telegram::editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'message_id' => $message->getMessageId(),
                'text' => $message->getText() . "\n\n🟢 *Status:* {$action}d by {$approverName}",
                'parse_mode' => 'Markdown'
            ]);

            // Send confirmation to user
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

        // Alternative: Send email notification
        // $leaveRequest->user->notify(new LeaveRequestProcessed($leaveRequest));
    }

    private function handleMessage($message)
    {
        // Optional: Handle direct messages to bot
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