<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $userRole = session('user_role', 'viewer');
        
        // Admin has access to everything
        if ($userRole === 'admin') {
            return $next($request);
        }
        
        // Check specific role requirement
        if ($role === 'admin' && $userRole !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        if ($role === 'operator' && !in_array($userRole, ['admin', 'operator'])) {
            abort(403, 'Unauthorized. Operator or Admin access required.');
        }
        
        if ($role === 'viewer' && !in_array($userRole, ['admin', 'operator', 'viewer'])) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}


