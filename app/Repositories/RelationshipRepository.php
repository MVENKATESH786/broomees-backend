<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class RelationshipRepository
{
    public function addFriend(string $userId, string $friendId)
    {
        // Enforce Mutual Relationship (A->B and B->A)
        DB::transaction(function () use ($userId, $friendId) {
            DB::table('relationships')->insertOrIgnore([
                ['user_id' => $userId, 'friend_id' => $friendId, 'created_at' => now()],
                ['user_id' => $friendId, 'friend_id' => $userId, 'created_at' => now()],
            ]);
        });
    }

    public function removeFriend(string $userId, string $friendId)
    {
        DB::transaction(function () use ($userId, $friendId) {
            DB::table('relationships')->where('user_id', $userId)->where('friend_id', $friendId)->delete();
            DB::table('relationships')->where('user_id', $friendId)->where('friend_id', $userId)->delete();
        });
    }

    public function exists(string $userId, string $friendId)
    {
        return DB::table('relationships')
            ->where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->exists();
    }
}
