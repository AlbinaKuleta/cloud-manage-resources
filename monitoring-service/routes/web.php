<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetricsController;

Route::get('/metrics', function () {
    return view('metrics');    // UI me grafikun
});

// API endpoints - duhet të jenë të disponueshme
Route::get('/api/metrics', [MetricsController::class, 'getMetrics']);
Route::get('/api/status', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'monitoring-service',
        'timestamp' => now()->toIso8601String()
    ]);
});
