<?php

namespace App\Http\Controllers\web\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{

    public function index()
    {
        $conversations = Conversation::where('school_admin_id', Auth::id())
            ->with(['student', 'messages' => function ($query) {
                $query->latest()->first();
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('admin.chat.index', compact('conversations'));
    }

    public function show($conversationId)
    {
        $conversation = Conversation::where('id', $conversationId)
            ->where('school_admin_id', Auth::id())
            ->with(['student', 'messages.sender'])
            ->firstOrFail();

        // Mark messages as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $conversation->messages,
            'student' => $conversation->student,
        ]);
    }

    public function storeMessage(Request $request, $conversationId)
    {
        $conversation = Conversation::where('id', $conversationId)
            ->where('school_admin_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => Auth::id(),
            'content' => $request->content,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Broadcast event
        event(new \App\Events\MessageSent($message));

        return response()->json($message, 201);
    }
}
