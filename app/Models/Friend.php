<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'friend_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }


    public static function isFriend($userId1, $userId2)
    {
        return self::where(function ($query) use ($userId1, $userId2) {
            $query->where('user_id', $userId1)
                ->where('friend_id', $userId2);
        })
            ->orWhere(function ($query) use ($userId1, $userId2) {
                $query->where('user_id', $userId2)
                    ->where('friend_id', $userId1);
            })
            ->where('status', 'accepted')
            ->exists();
    }
}
