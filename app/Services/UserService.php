<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function deleteUser(string $id)
    {
        $user = $this->userRepo->find($id);

        // Deletion Rule 1: Active Relationships
        if ($user->friends()->count() > 0) {
            throw new \Exception("Cannot delete user with active relationships", 409);
        }

        // Deletion Rule 2: High Reputation
        // Configurable threshold via .env (default 50)
        $threshold = config('app.reputation_threshold', 50);
        if ($user->reputation_score > $threshold) {
            throw new \Exception("Cannot delete user with high reputation", 409);
        }

        $this->userRepo->delete($user);
    }
}
