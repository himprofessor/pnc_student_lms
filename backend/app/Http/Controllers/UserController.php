<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Link the authenticated user's account to a Telegram chat ID.
     */
    public function linkTelegramAccount(Request $request)
    {
        Log::info('Received request to link Telegram account.');

        // Check for an authenticated user
        if (!Auth::check()) {
            Log::warning('Unauthorized attempt to link Telegram account.');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate the incoming request data
        $validated = $request->validate([
            'telegram_chat_id' => 'required|string',
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Update the user's telegram_chat_id
        $user->telegram_chat_id = $validated['telegram_chat_id'];
        $user->save();
        
        Log::info('Telegram account linked successfully for user ID: ' . $user->id);

        return response()->json(['message' => 'Telegram account linked successfully!', 'user' => $user]);
    }
}
