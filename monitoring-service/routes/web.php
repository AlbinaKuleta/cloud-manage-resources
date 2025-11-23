<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetricsController;

Route::get('/metrics', function () {
    return view('metrics');    // UI me grafikun
});

// API endpoint
Route::get('/api/metrics', [MetricsController::class, 'getMetrics']);
