<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class LoginPage extends Component
{
    public $email = '';
    public $password = '';
    public $errorMessage = '';

    public function login()
    {
        $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');
        
        $response = Http::timeout(30)->post("{$authServiceUrl}/api/login", [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($response->failed()) {
            $this->errorMessage = 'Invalid credentials';
            return;
        }

        $data = $response->json();
        $user = $data['user'] ?? [];

        // Store token and user info including role
        session([
            'token' => $data['token'],
            'user' => $user,
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? null,
            'user_email' => $user['email'] ?? null,
            'user_role' => $user['role'] ?? 'viewer',
        ]);

        // Log login activity
        $this->logActivity('Login', 'Hyrje në sistem', request()->ip());

        return redirect('/dashboard');
    }

    private function logActivity($action, $description, $ip = null)
    {
        $logs = session('user_activity_logs', []);
        
        // Add new log entry
        array_unshift($logs, [
            'action' => $action,
            'description' => $description,
            'time' => now()->toDateTimeString(), // Store as string for session
            'ip' => $ip ?? request()->ip(),
        ]);

        // Keep only last 20 logs
        $logs = array_slice($logs, 0, 20);

        session(['user_activity_logs' => $logs]);
    }

    public function render()
    {
        return view('livewire.login-page')->layout('components.layouts.auth');
    }
}
