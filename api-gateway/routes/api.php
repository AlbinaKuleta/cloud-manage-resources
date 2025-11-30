<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GatewayController;

/*
|--------------------------------------------------------------------------
| API Gateway Routes
|--------------------------------------------------------------------------
|
| Të gjitha kërkesat API përmes gateway
|
*/

// Health check endpoint (pa middleware)
Route::get('/health', [GatewayController::class, 'health']);

// Auth Service Routes (pa authentication për login/register)
Route::prefix('auth')->group(function () {
    Route::post('/login', [GatewayController::class, 'auth']);
    Route::post('/register', [GatewayController::class, 'auth']);
    
    // Routes që kërkojnë authentication
    Route::middleware('gateway.auth')->group(function () {
        Route::get('/me', [GatewayController::class, 'auth']);
        Route::post('/logout', [GatewayController::class, 'auth']);
    });
});

// Resource Service Routes (kërkojnë authentication)
Route::prefix('resources')->middleware(['gateway.auth'])->group(function () {
    Route::get('{path?}', [GatewayController::class, 'resources'])->where('path', '.*');
    Route::post('{path?}', [GatewayController::class, 'resources'])->where('path', '.*');
    Route::put('{path?}', [GatewayController::class, 'resources'])->where('path', '.*');
    Route::patch('{path?}', [GatewayController::class, 'resources'])->where('path', '.*');
    Route::delete('{path?}', [GatewayController::class, 'resources'])->where('path', '.*');
});

// Monitoring Service Routes (kërkojnë authentication)
Route::prefix('monitoring')->middleware(['gateway.auth'])->group(function () {
    Route::get('{path?}', [GatewayController::class, 'monitoring'])->where('path', '.*');
    Route::post('{path?}', [GatewayController::class, 'monitoring'])->where('path', '.*');
});

// Autoscaler Service Routes (kërkojnë authentication, vetëm admin)
Route::prefix('autoscaler')->middleware(['gateway.auth', 'gateway.role:admin'])->group(function () {
    Route::get('{path?}', [GatewayController::class, 'autoscaler'])->where('path', '.*');
    Route::post('{path?}', [GatewayController::class, 'autoscaler'])->where('path', '.*');
    Route::put('{path?}', [GatewayController::class, 'autoscaler'])->where('path', '.*');
    Route::delete('{path?}', [GatewayController::class, 'autoscaler'])->where('path', '.*');
});

