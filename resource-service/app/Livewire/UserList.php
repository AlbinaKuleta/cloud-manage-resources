<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserList extends Component
{
    public $users = [];
    public $loading = true;
    public $error = null;
    public $showDeleteModal = false;
    public $deleteUserId = null;
    public $deleteUserName = null;
    
    // Create modal
    public $showCreateModal = false;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'viewer';
    public $createLoading = false;
    
    // Edit modal
    public $showEditModal = false;
    public $editUserId = null;
    public $editUserName = '';
    public $editUserEmail = '';
    public $currentRole = '';
    public $newRole = 'viewer';
    public $editLoading = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|min:6|confirmed',
        'role' => 'required|in:admin,operator,viewer',
        'newRole' => 'required|in:admin,operator,viewer',
    ];

    protected $messages = [
        'name.required' => 'Emri është i detyrueshëm.',
        'email.required' => 'Email është i detyrueshëm.',
        'email.email' => 'Email duhet të jetë valid.',
        'password.required' => 'Fjalëkalimi është i detyrueshëm.',
        'password.min' => 'Fjalëkalimi duhet të ketë të paktën 6 karaktere.',
        'password.confirmed' => 'Konfirmimi i fjalëkalimit nuk përputhet.',
        'role.required' => 'Roli është i detyrueshëm.',
        'newRole.required' => 'Roli është i detyrueshëm.',
    ];

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $token = session('token');
            $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

            $response = Http::timeout(5)
                ->withToken($token)
                ->get("{$authServiceUrl}/api/admin/users");

            if ($response->successful()) {
                $this->users = $response->json()['users'] ?? [];
            } else {
                $this->error = 'Failed to load users. ' . ($response->json()['message'] ?? 'Unknown error');
                Log::error('UserList: Failed to load users', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            $this->error = 'Error loading users: ' . $e->getMessage();
            Log::error('UserList: Exception loading users', [
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function confirmDelete($userId, $userName)
    {
        $this->deleteUserId = $userId;
        $this->deleteUserName = $userName;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteUserId = null;
        $this->deleteUserName = null;
    }

    public function deleteUser()
    {
        if (!$this->deleteUserId) {
            return;
        }

        try {
            $token = session('token');
            $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

            $response = Http::timeout(5)
                ->withToken($token)
                ->delete("{$authServiceUrl}/api/admin/users/{$this->deleteUserId}");

            if ($response->successful()) {
                session()->flash('success', 'User deleted successfully');
                $this->loadUsers();
            } else {
                $errorMessage = $response->json()['message'] ?? 'Failed to delete user';
                session()->flash('error', $errorMessage);
                Log::error('UserList: Failed to delete user', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting user: ' . $e->getMessage());
            Log::error('UserList: Exception deleting user', [
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->showDeleteModal = false;
            $this->deleteUserId = null;
            $this->deleteUserName = null;
        }
    }

    // Create modal methods
    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    public function resetCreateForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'viewer';
        $this->resetErrorBag();
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,operator,viewer',
        ]);

        $this->createLoading = true;

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
                $this->closeCreateModal();
                $this->loadUsers();
            } else {
                $errorMessage = $response->json()['message'] ?? 'Failed to create user';
                session()->flash('error', $errorMessage);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating user: ' . $e->getMessage());
        } finally {
            $this->createLoading = false;
        }
    }

    // Edit modal methods
    public function openEditModal($userId)
    {
        $user = collect($this->users)->firstWhere('id', $userId);
        if ($user) {
            $this->editUserId = $userId;
            $this->editUserName = $user['name'];
            $this->editUserEmail = $user['email'];
            $this->currentRole = $user['role'];
            $this->newRole = $user['role'];
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editUserId = null;
        $this->editUserName = '';
        $this->editUserEmail = '';
        $this->currentRole = '';
        $this->newRole = 'viewer';
        $this->resetErrorBag();
    }

    public function updateRole()
    {
        $this->validate([
            'newRole' => 'required|in:admin,operator,viewer',
        ]);

        // Prevent changing own role
        if ($this->editUserId == session('user_id')) {
            session()->flash('error', 'Nuk mund të ndryshoni rolin tuaj.');
            return;
        }

        $this->editLoading = true;

        try {
            $token = session('token');
            $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

            $response = Http::timeout(5)
                ->withToken($token)
                ->patch("{$authServiceUrl}/api/admin/users/{$this->editUserId}/role", [
                    'role' => $this->newRole,
                ]);

            if ($response->successful()) {
                session()->flash('success', 'Roli i përdoruesit u përditësua me sukses!');
                $this->closeEditModal();
                $this->loadUsers();
            } else {
                $errorMessage = $response->json()['message'] ?? 'Failed to update role';
                session()->flash('error', $errorMessage);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating role: ' . $e->getMessage());
        } finally {
            $this->editLoading = false;
        }
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

        return view('livewire.user-list');
    }
}


