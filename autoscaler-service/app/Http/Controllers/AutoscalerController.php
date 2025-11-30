<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MonitoringService;
use App\Services\ScalingService;
use App\Models\ScalingHistory;
use Illuminate\Support\Facades\Log;

/**
 * AutoscalerController - API endpoints për autoscaler service
 */
class AutoscalerController extends Controller
{
    private $monitoringService;
    private $scalingService;

    public function __construct(MonitoringService $monitoringService, ScalingService $scalingService)
    {
        $this->monitoringService = $monitoringService;
        $this->scalingService = $scalingService;
    }

    /**
     * Merr statusin e autoscaler service
     */
    public function status()
    {
        $metrics = $this->monitoringService->getMetrics();
        $resourceService = app(\App\Services\ResourceService::class);
        $resources = $resourceService->getResources() ?? [];
        $autoScaledResources = array_filter($resources, function ($resource) {
            return isset($resource['name']) && str_starts_with($resource['name'], 'auto-scaled-');
        });

        return response()->json([
            'status' => 'operational',
            'enabled' => config('autoscaler.scaling.enabled', true),
            'current_metrics' => $metrics,
            'auto_scaled_resources_count' => count($autoScaledResources),
            'total_resources' => count($resources ?? []),
            'thresholds' => [
                'scale_up' => config('autoscaler.thresholds.scale_up'),
                'scale_down' => config('autoscaler.thresholds.scale_down'),
            ],
            'scaling_limits' => [
                'min' => config('autoscaler.scaling.min_resources', 1),
                'max' => config('autoscaler.scaling.max_resources', 10),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Merr historikun e scaling
     */
    public function history()
    {
        $history = ScalingHistory::orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'history' => $history,
            'count' => $history->count(),
        ]);
    }

    /**
     * Manual scale up (vetëm admin)
     */
    public function scaleUp(Request $request)
    {
        $success = $this->scalingService->executeScaleUp();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Scale up u ekzekutua me sukses',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Scale up dështoi. Kontrollo log-et për më shumë detaje.',
            ], 500);
        }
    }

    /**
     * Manual scale down (vetëm admin)
     */
    public function scaleDown(Request $request)
    {
        $success = $this->scalingService->executeScaleDown();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Scale down u ekzekutua me sukses',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Scale down dështoi. Kontrollo log-et për më shumë detaje.',
            ], 500);
        }
    }

    /**
     * Analizon metrikat dhe kthen vendimin
     */
    public function analyze()
    {
        $result = $this->scalingService->analyzeMetrics();

        return response()->json([
            'analysis' => $result,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

