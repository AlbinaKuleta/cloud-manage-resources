<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * RateLimiter - Kufizon numrin e kërkesave për IP
 */
class RateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kontrollo nëse rate limiting është aktiv
        if (!config('gateway.rate_limit.enabled', true)) {
            return $next($request);
        }

        $maxRequests = config('gateway.rate_limit.max_requests', 100);
        $decayMinutes = config('gateway.rate_limit.decay_minutes', 1);
        
        // Merr IP-në e klientit
        $ip = $request->ip();
        $key = "rate_limit_{$ip}";

        // Merr numrin aktual të kërkesave
        $requests = Cache::get($key, 0);

        if ($requests >= $maxRequests) {
            Log::warning("Gateway: Rate limit exceeded", [
                'ip' => $ip,
                'requests' => $requests,
                'max' => $maxRequests,
            ]);

            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $decayMinutes * 60
            ], 429);
        }

        // Rrit numrin e kërkesave
        Cache::put($key, $requests + 1, now()->addMinutes($decayMinutes));

        // Shto headers për rate limit info
        $response = $next($request);
        
        $remaining = $maxRequests - ($requests + 1);
        $response->headers->set('X-RateLimit-Limit', $maxRequests);
        $response->headers->set('X-RateLimit-Remaining', max(0, $remaining));
        $response->headers->set('X-RateLimit-Reset', now()->addMinutes($decayMinutes)->timestamp);

        return $response;
    }
}

