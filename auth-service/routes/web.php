<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::middleware('api')->prefix('api')->group(function () {

    // Public API
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected API
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // Admin-only endpoints
        Route::middleware('role.admin')->prefix('admin')->group(function () {
            Route::post('/users/create', [AdminController::class, 'createUser']);
            Route::get('/users', [AdminController::class, 'listUsers']);
            Route::patch('/users/{id}/role', [AdminController::class, 'updateUserRole']);
            Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        });
    });

});
