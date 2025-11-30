<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthToken
{
    private $authServiceUrl;

    public function __construct()
    {
        $this->authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');
    }

    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('token')) {
            return redirect('/login');
        }

        $token = session('token');
        
        // Validate token with auth-service and get user info including role
        try {
            $response = Http::timeout(5)
                ->withToken($token)
                ->get("{$this->authServiceUrl}/api/me");

            if ($response->successful()) {
                $userData = $response->json()['user'] ?? null;
                
                if ($userData) {
                    // Store user info in session for Livewire access
                    session([
                        'user_id' => $userData['id'],
                        'user_name' => $userData['name'],
                        'user_email' => $userData['email'],
                        'user_role' => $userData['role'] ?? 'viewer',
                    ]);
                    
                    // Also attach to request for easy access
                    $request->merge([
                        'user_id' => $userData['id'],
                        'user_name' => $userData['name'],
                        'user_email' => $userData['email'],
                        'user_role' => $userData['role'] ?? 'viewer',
                    ]);

                    // Log page view activity (only for main pages, not API calls)
                    if ($request->isMethod('GET') && !$request->is('api/*')) {
                        $this->logPageView($request);
                    }
                } else {
                    Log::warning('AuthToken: User data not found in response');
                    session()->forget('token');
                    return redirect('/login')->with('error', 'Session expired. Please login again.');
                }
            } else {
                // Token invalid or expired
                Log::warning('AuthToken: Token validation failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                session()->forget('token');
                return redirect('/login')->with('error', 'Session expired. Please login again.');
            }
        } catch (\Exception $e) {
            Log::error('AuthToken: Error validating token', [
                'error' => $e->getMessage()
            ]);
            // If auth-service is down, allow access but with viewer role as fallback
            if (!session()->has('user_role')) {
                session(['user_role' => 'viewer']);
            }
        }

        return $next($request);
    }

    private function logPageView(Request $request)
    {
        $routeName = $request->route()->getName() ?? $request->path();
        $pageName = $this->getPageName($request->path());
        
        if ($pageName) {
            $logs = session('user_activity_logs', []);
            
            // Don't log if same page viewed within last 30 seconds
            $lastLog = $logs[0] ?? null;
            if ($lastLog && 
                isset($lastLog['action']) && 
                $lastLog['action'] === "View {$pageName}" &&
                isset($lastLog['time']) &&
                now()->diffInSeconds($lastLog['time']) < 30) {
                return;
            }

            // Add new log entry
            array_unshift($logs, [
                'action' => "View {$pageName}",
                'description' => "Shikim i faqes: {$pageName}",
                'time' => now()->toDateTimeString(), // Store as string for session
                'ip' => $request->ip(),
            ]);

            // Keep only last 20 logs
            $logs = array_slice($logs, 0, 20);

            session(['user_activity_logs' => $logs]);
        }
    }

    private function getPageName($path)
    {
        $pageNames = [
            'dashboard' => 'Dashboard',
            'resources' => 'Resources',
            'metrics' => 'Metrics',
            'users' => 'Users',
            'communication' => 'Communication',
            'rbac-test' => 'RBAC Test',
        ];

        foreach ($pageNames as $route => $name) {
            if (str_contains($path, $route)) {
                return $name;
            }
        }

        return null;
    }
}
