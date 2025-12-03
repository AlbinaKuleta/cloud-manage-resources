<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutoscalerController;

/*
|--------------------------------------------------------------------------
| Autoscaler Service API Routes
|--------------------------------------------------------------------------
|
| API endpoints për autoscaler service
|
*/

// Status endpoint (publike për health check)
Route::get('/status', [AutoscalerController::class, 'status']);

// Analyze endpoint (publike për monitoring)
Route::get('/analyze', [AutoscalerController::class, 'analyze']);

// Të gjitha routes e tjera kërkojnë authentication (nëse implementohet)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/history', [AutoscalerController::class, 'history']);
    Route::post('/scale-up', [AutoscalerController::class, 'scaleUp']);
    Route::post('/scale-down', [AutoscalerController::class, 'scaleDown']);
});

