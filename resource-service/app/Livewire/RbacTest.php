<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RbacTest extends Component
{
    public $userInfo = [];
    public $tokenStatus = 'unknown';
    public $authServiceStatus = 'unknown';
    public $testResults = [];

    public function mount()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        $this->loadUserInfo();
        $this->testTokenValidation();
    }

    public function loadUserInfo()
    {
        $this->userInfo = [
            'id' => session('user_id'),
            'name' => session('user_name'),
            'email' => session('user_email'),
            'role' => session('user_role', 'viewer'),
        ];
    }

    public function testTokenValidation()
    {
        $this->tokenStatus = 'testing';
        
        try {
            $token = session('token');
            $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

            $response = Http::timeout(5)
                ->withToken($token)
                ->get("{$authServiceUrl}/api/me");

            if ($response->successful()) {
                $userData = $response->json()['user'] ?? null;
                $this->tokenStatus = 'valid';
                $this->testResults['token_validation'] = [
                    'status' => 'success',
                    'message' => 'Token is valid',
                    'user_data' => $userData,
                ];
            } else {
                $this->tokenStatus = 'invalid';
                $this->testResults['token_validation'] = [
                    'status' => 'failed',
                    'message' => 'Token validation failed',
                    'status_code' => $response->status(),
                ];
            }
        } catch (\Exception $e) {
            $this->tokenStatus = 'error';
            $this->testResults['token_validation'] = [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    public function testRoleDetection()
    {
        $this->testResults['role_detection'] = [
            'session_role' => session('user_role', 'not_set'),
            'user_id' => session('user_id'),
            'user_name' => session('user_name'),
            'user_email' => session('user_email'),
            'status' => 'success',
        ];
    }

    public function testCrossServiceCommunication()
    {
        try {
            $token = session('token');
            $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

            // Test admin endpoint
            $response = Http::timeout(5)
                ->withToken($token)
                ->get("{$authServiceUrl}/api/admin/users");

            if ($response->successful()) {
                $users = $response->json()['users'] ?? [];
                $this->testResults['cross_service'] = [
                    'status' => 'success',
                    'message' => 'Successfully communicated with auth-service',
                    'users_count' => count($users),
                ];
                $this->authServiceStatus = 'connected';
            } else {
                $this->testResults['cross_service'] = [
                    'status' => 'failed',
                    'message' => 'Failed to communicate with auth-service',
                    'status_code' => $response->status(),
                ];
                $this->authServiceStatus = 'disconnected';
            }
        } catch (\Exception $e) {
            $this->testResults['cross_service'] = [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ];
            $this->authServiceStatus = 'error';
        }
    }

    public function runAllTests()
    {
        $this->testTokenValidation();
        $this->testRoleDetection();
        $this->testCrossServiceCommunication();
    }

    public function getRoleBadgeClass($role)
    {
        return match($role) {
            'admin' => 'bg-red-100 text-red-800 border-red-200',
            'operator' => 'bg-blue-100 text-blue-800 border-blue-200',
            'viewer' => 'bg-gray-100 text-gray-800 border-gray-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function render()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return view('livewire.rbac-test');
    }
}


