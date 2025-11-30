<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        $token = session('token');
        $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

        // Call auth-service logout endpoint if token exists
        if ($token) {
            try {
                Http::timeout(5)
                    ->withToken($token)
                    ->post("{$authServiceUrl}/api/logout");
            } catch (\Exception $e) {
                // Continue with logout even if auth-service is unavailable
            }
        }

        // Clear all session data
        Session::flush();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}

