<?php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReputationService
{
    public function calculateAndSave(User $user)
    {
        // Must be transaction safe
        DB::transaction(function () use ($user) {
            // 1. Unique Friends (unblocked)
            $friendCount = $user->friends()->wherePivot('is_blocked', false)->count();

            // 2. Shared Hobbies
            // (Simplified logic: Count hobbies shared with all friends)
            $userHobbies = $user->hobbies->pluck('id')->toArray();
            $sharedHobbiesScore = 0;

            if (!empty($userHobbies)) {
                $friends = $user->friends;
                foreach ($friends as $friend) {
                    $friendHobbies = $friend->hobbies->pluck('id')->toArray();
                    $intersection = count(array_intersect($userHobbies, $friendHobbies));
                    $sharedHobbiesScore += ($intersection * 0.5);
                }
            }

            // 3. Account Age
            $days = Carbon::parse($user->created_at)->diffInDays(now());
            $ageScore = min(3, $days / 30);

            // 4. Blocked
            $blockedCount = $user->friends()->wherePivot('is_blocked', true)->count();

            $total = $friendCount + $sharedHobbiesScore + $ageScore - $blockedCount;

            // Direct update to prevent version conflicts with main user logic
            DB::table('users')->where('id', $user->id)->update(['reputation_score' => max(0, $total)]);
        });
    }
}
