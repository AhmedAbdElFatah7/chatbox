<?php

namespace App\Http\Controllers\Friends;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    use ApiResponse;

    public function search(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
        ]);

        $users = User::where('username', 'like', '%' . $request->username . '%')
            ->where('id', '!=', auth()->id())
            ->get();

        if ($users->isEmpty()) {
            return $this->error([], "No users found");
        }

        return $this->success($users, "Users found", 200);
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);
        if ($request->user()->id == $request->friend_id) {
            return $this->error([], "You cannot add yourself");
        }
        $exists = Friend::where([
            ['user_id', $request->user()->id],
            ['friend_id', $request->friend_id],
        ])
            ->orWhere([
                ['user_id', $request->friend_id],
                ['friend_id', $request->user()->id],
            ])
            ->exists();
        if ($exists) {
            return $this->error([], "Friend request already exists");
        }
        $friendship = Friend::create([
            'user_id' => $request->user()->id,
            'friend_id' => $request->friend_id,
            'status' => 'pending',
        ]);

        return $this->success($friendship, "Friend request sent");
    }
    public function acceptRequest(Request $request)
    {
        $friendship = Friend::where('user_id', $request->user_id)
            ->where('friend_id', auth()->id())
            ->firstOrFail();
        $friendship->update(['status' => 'accepted']);
        return $this->success($friendship, "Friend request accepted");
    }

    public function myFriends()
    {
        $friends = auth()->user()->all_friends;
        return $this->success($friends, "My friends list");
    }

    public function friendRequests()
    {
        $ids = Friend::where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->pluck('user_id');

        $users = User::whereIn('id', $ids)->get();

        return $this->success($users, "Friend requests received");
    }
}
