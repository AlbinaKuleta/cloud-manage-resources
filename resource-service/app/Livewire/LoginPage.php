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
        $response = Http::post('http://127.0.0.1:8000/api/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($response->failed()) {
            $this->errorMessage = 'Invalid credentials';
            return;
        }

        $data = $response->json();

        session(['token' => $data['token']]);
        session(['user' => $data['user']]);

        return redirect('/resources');
    }

    public function render()
    {
        return view('livewire.login-page');
    }
}
