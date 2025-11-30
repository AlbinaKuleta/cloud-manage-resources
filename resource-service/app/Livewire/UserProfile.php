<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class UserProfile extends Component
{
    public $userName;
    public $userEmail;
    public $userRole;
    public $userId;
    public $activityLogs = [];

    public function mount()
    {
        $this->loadUserData();
        $this->loadActivityLogs();
    }

    public function loadUserData()
    {
        $this->userName = session('user_name', 'User');
        $this->userEmail = session('user_email', '');
        $this->userRole = session('user_role', 'viewer');
        $this->userId = session('user_id');
    }

    public function loadActivityLogs()
    {
        // Load activity logs from session
        $logs = session('user_activity_logs', []);
        
        // Convert time to Carbon instances if needed
        $this->activityLogs = collect($logs)->map(function ($log) {
            if (isset($log['time'])) {
                if (is_string($log['time'])) {
                    $log['time'] = Carbon::parse($log['time']);
                } elseif (is_array($log['time'])) {
                    $log['time'] = Carbon::parse($log['time']['date']);
                } elseif (!$log['time'] instanceof Carbon) {
                    $log['time'] = now();
                }
            } else {
                $log['time'] = now();
            }
            return $log;
        })->toArray();
    }

    public function getRoleBadgeClass()
    {
        return match($this->userRole) {
            'admin' => 'bg-red-100 text-red-800 border-red-200',
            'operator' => 'bg-blue-100 text-blue-800 border-blue-200',
            'viewer' => 'bg-gray-100 text-gray-800 border-gray-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}

