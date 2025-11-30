<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * EnsureRole - Verifikon nëse përdoruesi ka role të kërkuar
 */
class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $userRole = $request->user_role ?? session('user_role', 'viewer');

        // Admin ka akses në të gjitha
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Kontrollo nëse përdoruesi ka role të kërkuar
        if ($userRole !== $role) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => "Access denied. Required role: {$role}"
            ], 403);
        }

        return $next($request);
    }
}

