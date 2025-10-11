<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Models\Friend;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    use ApiResponse;

    /**
     * Send a message to another user
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation failed", 422);
        }
        
        $user = $request->user();
        $receiverId = $request->receiver_id;
        
        // Check if users are friends
        $isFriend = Friend::isFriend($user->id, $receiverId);
        if (!$isFriend) {
            return $this->error('You can only send messages to your friends', 403);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        $message->load(['sender', 'receiver']);

        broadcast(new MessageSent($message));

        return $this->success([
            'message' => $message,
        ], 'Message sent successfully');
    }

    public function getConversation(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $request->user_id;
        
        // Check if users are friends
        $isFriend = Friend::isFriend($user->id, $userId);
        if (!$isFriend) {
            return $this->error('You can only view conversations with your friends', 403);
        }
        $messages = Message::where(function ($query) use ($user, $userId) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($user, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $user->id);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        return $this->success([
            'messages' => $messages,
            'conversation_with' => User::find($userId),
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $request->user_id;
        Message::where('sender_id', $userId)
               ->where('receiver_id', $user->id)
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return $this->success([], 'Messages marked as read');
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        $unreadCount = Message::where('receiver_id', $user->id)
                             ->where('is_read', false)
                             ->count();

        return $this->success([
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get recent conversations
     */
    public function getRecentConversations(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = Message::where('sender_id', $user->id)
                               ->orWhere('receiver_id', $user->id)
                               ->with(['sender', 'receiver'])
                               ->orderBy('created_at', 'desc')
                               ->get()
                               ->groupBy(function ($message) use ($user) {
                                   return $message->sender_id == $user->id 
                                       ? $message->receiver_id 
                                       : $message->sender_id;
                               })
                               ->map(function ($messages) use ($user) {
                                   $latestMessage = $messages->first();
                                   $otherUser = $latestMessage->sender_id == $user->id 
                                       ? $latestMessage->receiver 
                                       : $latestMessage->sender;
                                   
                                   $unreadCount = $messages->where('receiver_id', $user->id)
                                                          ->where('is_read', false)
                                                          ->count();

                                   return [
                                       'user' => $otherUser,
                                       'latest_message' => $latestMessage,
                                       'unread_count' => $unreadCount,
                                   ];
                               })
                               ->values();

        return $this->success([
            'conversations' => $conversations,
        ]);
    }
}
