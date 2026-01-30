<?php
namespace App\Repositories;

use App\Models\Token;
use Illuminate\Support\Str;

class TokenRepository
{
    public function createToken()
    {
        $plainText = Str::random(60);
        Token::create([
            'token_hash' => hash('sha256', $plainText),
            'expires_at' => now()->addHours(24)
        ]);
        return $plainText;
    }

    public function isValid(string $plainTextToken)
    {
        return Token::where('token_hash', hash('sha256', $plainTextToken))
            ->where('expires_at', '>', now())
            ->exists();
    }
}
