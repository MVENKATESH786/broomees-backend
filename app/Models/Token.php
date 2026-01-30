<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'access_tokens';
    protected $fillable = ['token_hash', 'expires_at'];
}
