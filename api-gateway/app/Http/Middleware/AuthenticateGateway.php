<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;

/**
 * AuthenticateGateway - Verifikon token-in për kërkesat që kërkojnë autentifikim
 */
class AuthenticateGateway
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Merr token nga header ose session
        $token = $request->bearerToken() ?? $request->header('Authorization');
        
        if ($token) {
            // Nëse është "Bearer token", hiq "Bearer "
            $token = str_replace('Bearer ', '', $token);
        } else {
            // Provoni nga session
            $token = session('token');
        }

        if (!$token) {
            Log::warning("Gateway: No token provided", [
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Token is required'
            ], 401);
        }

        // Validon token-in
        $user = $this->authService->validateToken($token);

        if (!$user) {
            Log::warning("Gateway: Invalid token", [
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or expired token'
            ], 401);
        }

        // Shto informacionin e përdoruesit në request
        $request->merge([
            'user' => $user,
            'user_id' => $user['id'] ?? null,
            'user_role' => $user['role'] ?? 'viewer',
        ]);

        // Ruaj në session për akses më të lehtë
        session([
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? null,
            'user_email' => $user['email'] ?? null,
            'user_role' => $user['role'] ?? 'viewer',
        ]);

        return $next($request);
    }
}

