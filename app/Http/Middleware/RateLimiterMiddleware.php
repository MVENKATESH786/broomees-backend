<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->ip();

        $isWrite = in_array($request->method(), ['POST', 'PUT', 'DELETE']);
        $limit = $isWrite ? 30 : 120;

        // Separate buckets for read vs write
        $key = 'rate_limit:' . $token . ($isWrite ? ':write' : ':read');

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return response()->json(['message' => 'Too Many Requests'], 429);
        }

        RateLimiter::hit($key, 60); // 60 seconds decay

        return $next($request);
    }
}
