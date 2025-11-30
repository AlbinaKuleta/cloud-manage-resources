<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserCreate extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'viewer';
    public $loading = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|min:6|confirmed',
        'role' => 'required|in:admin,operator,viewer',
    ];

    protected $messages = [
        'name.required' => 'Emri është i detyrueshëm.',
        'email.required' => 'Email është i detyrueshëm.',
        'email.email' => 'Email duhet të jetë valid.',
        'password.required' => 'Fjalëkalimi është i detyrueshëm.',
        'password.min' => 'Fjalëkalimi duhet të ketë të paktën 6 karaktere.',
        'password.confirmed' => 'Konfirmimi i fjalëkalimit nuk përputhet.',
        'role.required' => 'Roli është i detyrueshëm.',
        'role.in' => 'Roli i zgjedhur nuk është valid.',
    ];

    public function save()
    {
        $this->validate();

        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $this->loading = true;

        try {
            $token = session('token');
            $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

            $response = Http::timeout(5)
                ->withToken($token)
                ->post("{$authServiceUrl}/api/admin/users/create", [
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password,
                    'role' => $this->role,
                ]);

            if ($response->successful()) {
                session()->flash('success', 'Përdoruesi u krijua me sukses!');
                return redirect()->route('users');
            } else {
                $errorMessage = $response->json()['message'] ?? 'Failed to create user';
                session()->flash('error', $errorMessage);
                Log::error('UserCreate: Failed to create user', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating user: ' . $e->getMessage());
            Log::error('UserCreate: Exception creating user', [
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return view('livewire.user-create');
    }
}


