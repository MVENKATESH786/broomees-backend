<?php
namespace App\Repositories;

use App\Models\Hobby;
use App\Models\User;

class HobbyRepository
{
    public function findByName(string $name)
    {
        return Hobby::firstOrCreate(['name' => $name]);
    }

    public function attachToUser(User $user, Hobby $hobby)
    {
        $user->hobbies()->syncWithoutDetaching([$hobby->id]);
    }
}
