<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetricsController;

Route::middleware('api')->prefix('api')->group(function () {

    Route::get('/metrics/{resourceId}', [MetricsController::class, 'getMetrics']);

});
