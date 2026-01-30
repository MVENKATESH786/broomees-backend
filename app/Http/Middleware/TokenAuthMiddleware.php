<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Repositories\TokenRepository;

class TokenAuthMiddleware
{
    protected $tokenRepo;

    public function __construct(TokenRepository $tokenRepo)
    {
        $this->tokenRepo = $tokenRepo;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token || !$this->tokenRepo->isValid($token)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
