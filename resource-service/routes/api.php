<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ResourceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API routes për resource-service. Këto routes mund të përdoren nga
| autoscaler-service dhe api-gateway për komunikim me resource-service.
|
*/

// API Routes për resurset
// Për autoscaler-service, do të lejojmë akses pa authentication për komunikim të brendshëm
Route::prefix('resources')->group(function () {
    Route::get('/', [ResourceController::class, 'index']);
    Route::get('/{id}', [ResourceController::class, 'show']);
    Route::post('/', [ResourceController::class, 'store']);
    Route::put('/{id}', [ResourceController::class, 'update']);
    Route::patch('/{id}', [ResourceController::class, 'update']);
    Route::delete('/{id}', [ResourceController::class, 'destroy']);
});

