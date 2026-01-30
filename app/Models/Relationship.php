<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    protected $table = 'relationships';
    public $timestamps = false;
    protected $fillable = ['user_id', 'friend_id', 'is_blocked', 'created_at'];
}
