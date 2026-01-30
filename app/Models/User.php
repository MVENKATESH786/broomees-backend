<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['username', 'age', 'reputation_score', 'version'];

    public function hobbies()
    {
        return $this->belongsToMany(Hobby::class, 'hobby_user');
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'relationships', 'user_id', 'friend_id')
            ->withPivot('is_blocked');
    }
}
