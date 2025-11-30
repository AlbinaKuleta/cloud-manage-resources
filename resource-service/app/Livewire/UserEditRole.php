<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserEditRole extends Component
{
    public $userId;
    public $userName;
    public $userEmail;
    public $currentRole;
    public $newRole = 'viewer';
    public $loading = false;
    public $loadingUser = true;

    public function mount($id)
    {
        $this->userId = $id;
        $this->loadUser();
    }

    public function loadUser()
    {
        $this->loadingUser = true;

        try {
            $token = session('token');
            $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

            $response = Http::timeout(5)
                ->withToken($token)
                ->get("{$authServiceUrl}/api/admin/users");

            if ($response->successful()) {
                $users = $response->json()['users'] ?? [];
                $user = collect($users)->firstWhere('id', $this->userId);

                if ($user) {
                    $this->userName = $user['name'];
                    $this->userEmail = $user['email'];
                    $this->currentRole = $user['role'];
                    $this->newRole = $user['role'];
                } else {
                    session()->flash('error', 'Përdoruesi nuk u gjet.');
                    return redirect()->route('users');
                }
            } else {
                session()->flash('error', 'Dështoi ngarkimi i të dhënave të përdoruesit.');
                return redirect()->route('users');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gabim në ngarkimin e të dhënave: ' . $e->getMessage());
            return redirect()->route('users');
        } finally {
            $this->loadingUser = false;
        }
    }

    public function updateRole()
    {
        $this->validate([
            'newRole' => 'required|in:admin,operator,viewer',
        ]);

        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Prevent changing own role
        if ($this->userId == session('user_id')) {
            session()->flash('error', 'Nuk mund të ndryshoni rolin tuaj.');
            return;
        }

        $this->loading = true;

        try {
            $token = session('token');
            $authServiceUrl = config('services.auth.url', 'http://127.0.0.1:8000');

            $response = Http::timeout(5)
                ->withToken($token)
                ->patch("{$authServiceUrl}/api/admin/users/{$this->userId}/role", [
                    'role' => $this->newRole,
                ]);

            if ($response->successful()) {
                session()->flash('success', 'Roli i përdoruesit u përditësua me sukses!');
                return redirect()->route('users');
            } else {
                $errorMessage = $response->json()['message'] ?? 'Failed to update role';
                session()->flash('error', $errorMessage);
                Log::error('UserEditRole: Failed to update role', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating role: ' . $e->getMessage());
            Log::error('UserEditRole: Exception updating role', [
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

        return view('livewire.user-edit-role');
    }
}


