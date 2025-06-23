<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\Conversation;

class ChatApiController extends Controller
{

    public function getConversations()
    {
        $user = Auth::user();
        if (!$user->hasRole('student')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversations = Conversation::where('student_id', $user->id)
            ->with(['schoolAdmin', 'messages' => function ($query) {
                $query->latest()->first();
            }])
            ->get();

        return response()->json($conversations);
    }

    public function getMessages($conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::where('id', $conversationId)
            ->where('student_id', $user->id)
            ->firstOrFail();

        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->get();

        // Mark messages as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::where('id', $conversationId)
            ->where('student_id', $user->id)
            ->firstOrFail();

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $user->id,
            'content' => $request->content,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Broadcast event
        event(new \App\Events\MessageSent($message));

        return response()->json($message, 201);
    }
}