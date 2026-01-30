<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class UserRepository
{
    public function paginate($limit = 15)
    {
        return User::paginate($limit);
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function updateWithLock(string $id, array $data, int $currentVersion)
    {
        return DB::transaction(function () use ($id, $data, $currentVersion) {
            $updated = DB::table('users')
                ->where('id', $id)
                ->where('version', $currentVersion)
                ->update(array_merge($data, [
                    'version' => $currentVersion + 1,
                    'updated_at' => now()
                ]));

            if ($updated === 0) {
                // Check if it was a version mismatch or ID not found
                $user = DB::table('users')->where('id', $id)->first();
                if ($user && $user->version !== $currentVersion) {
                    throw new Exception("Conflict: Data modified by another process", 409);
                }
                throw new Exception("User not found", 404);
            }

            return User::find($id);
        });
    }

    public function delete(User $user)
    {
        $user->delete();
    }
}
