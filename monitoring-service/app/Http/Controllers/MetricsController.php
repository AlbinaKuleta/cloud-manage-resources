<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function getMetrics($resourceId)
    {
        return response()->json([
            'resource_id' => $resourceId,
            'cpu' => rand(1, 100),         
            'ram' => rand(512, 8192),      
            'disk' => rand(10, 100),       
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
