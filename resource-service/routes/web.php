<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\LoginPage;
use App\Livewire\ResourceList;
use App\Livewire\ResourceCreate;
use App\Livewire\ResourceEdit;
use App\Livewire\MetricsComponent;
use App\Livewire\Dashboard;
use App\Http\Controllers\Api\MonitoringController;
use App\Http\Controllers\AuthController;


Route::get('/login', LoginPage::class)->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// API Routes për komunikimin me monitoring-service (për demonstrim)
Route::prefix('api')->group(function () {
    Route::get('/monitoring/status', [MonitoringController::class, 'getServiceStatus']);
    Route::get('/monitoring/metrics', [MonitoringController::class, 'getMetrics']);
    Route::post('/monitoring/check-capacity', [MonitoringController::class, 'checkCapacity']);
});

Route::middleware('auth.token')->group(function () {
    // Routes accessible by all roles
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class);
    Route::get('/resources', ResourceList::class);
    Route::get('/metrics', MetricsComponent::class)->name('metrics');
    Route::get('/profile', \App\Livewire\UserProfile::class)->name('profile');
    
    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/resources/create', ResourceCreate::class);
        Route::get('/resources/{id}/edit', ResourceEdit::class);
        Route::get('/users', \App\Livewire\UserList::class)->name('users');
        Route::get('/users/create', \App\Livewire\UserCreate::class)->name('users.create');
        Route::get('/users/{id}/edit-role', \App\Livewire\UserEditRole::class)->name('users.edit-role');
        Route::get('/rbac-test', \App\Livewire\RbacTest::class)->name('rbac-test');
        Route::get('/communication', \App\Livewire\ServiceCommunication::class)->name('communication');
    });
});
