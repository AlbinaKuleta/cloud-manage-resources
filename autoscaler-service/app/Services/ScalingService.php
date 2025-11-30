<?php

namespace App\Services;

use App\Models\ScalingHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * ScalingService - Logjika e auto-scaling
 */
class ScalingService
{
    private $monitoringService;
    private $resourceService;

    public function __construct(MonitoringService $monitoringService, ResourceService $resourceService)
    {
        $this->monitoringService = $monitoringService;
        $this->resourceService = $resourceService;
    }

    /**
     * Analizon metrikat dhe vendos nëse duhet scaling
     *
     * @return array|null ['action' => 'scale_up'|'scale_down'|null, 'reason' => string]
     */
    public function analyzeMetrics(): ?array
    {
        if (!config('autoscaler.scaling.enabled', true)) {
            return null;
        }

        $metrics = $this->monitoringService->getMetrics();
        
        if (!$metrics) {
            return null;
        }

        $cpu = $metrics['cpu_usage'] ?? 0;
        $memory = $metrics['memory_usage'] ?? 0;
        $disk = $metrics['disk_usage'] ?? 0;

        // Kontrollo për scale up
        $scaleUpResult = $this->checkScaleUp($cpu, $memory, $disk);
        if ($scaleUpResult) {
            return $scaleUpResult;
        }

        // Kontrollo për scale down
        $scaleDownResult = $this->checkScaleDown($cpu, $memory, $disk);
        if ($scaleDownResult) {
            return $scaleDownResult;
        }

        return null;
    }

    /**
     * Kontrollon nëse duhet scale up
     *
     * @param float $cpu
     * @param float $memory
     * @param float $disk
     * @return array|null
     */
    private function checkScaleUp(float $cpu, float $memory, float $disk): ?array
    {
        $thresholds = config('autoscaler.thresholds.scale_up');
        $duration = $thresholds['duration'] ?? 120;

        // Kontrollo nëse metrikat kanë kaluar threshold për kohë të mjaftueshme
        $cpuExceeded = $cpu > ($thresholds['cpu'] ?? 80);
        $memoryExceeded = $memory > ($thresholds['memory'] ?? 85);
        $diskExceeded = $disk > ($thresholds['disk'] ?? 90);

        if ($cpuExceeded || $memoryExceeded || $diskExceeded) {
            // Ruaj në cache që threshold është kaluar
            $key = 'scale_up_triggered';
            $triggered = Cache::get($key, false);
            $firstTriggered = Cache::get("{$key}_time", null);

            if (!$triggered) {
                // Së pari që kalohet threshold
                Cache::put($key, true, now()->addSeconds($duration));
                Cache::put("{$key}_time", now(), now()->addSeconds($duration));
                return null; // Prit deri sa të kalojë koha e nevojshme
            }

            // Kontrollo nëse ka kaluar koha e nevojshme
            if ($firstTriggered && now()->diffInSeconds($firstTriggered) >= $duration) {
                // Reset cache
                Cache::forget($key);
                Cache::forget("{$key}_time");

                $reasons = [];
                if ($cpuExceeded) $reasons[] = "CPU: {$cpu}% > {$thresholds['cpu']}%";
                if ($memoryExceeded) $reasons[] = "Memory: {$memory}% > {$thresholds['memory']}%";
                if ($diskExceeded) $reasons[] = "Disk: {$disk}% > {$thresholds['disk']}%";

                return [
                    'action' => 'scale_up',
                    'reason' => implode(', ', $reasons),
                    'metrics' => [
                        'cpu' => $cpu,
                        'memory' => $memory,
                        'disk' => $disk,
                    ],
                ];
            }
        } else {
            // Reset nëse metrikat nuk e kalojnë më threshold
            Cache::forget('scale_up_triggered');
            Cache::forget('scale_up_triggered_time');
        }

        return null;
    }

    /**
     * Kontrollon nëse duhet scale down
     *
     * @param float $cpu
     * @param float $memory
     * @param float $disk
     * @return array|null
     */
    private function checkScaleDown(float $cpu, float $memory, float $disk): ?array
    {
        $thresholds = config('autoscaler.thresholds.scale_down');
        $duration = $thresholds['duration'] ?? 300;

        // Kontrollo nëse të gjitha metrikat janë nën threshold
        $cpuBelow = $cpu < ($thresholds['cpu'] ?? 50);
        $memoryBelow = $memory < ($thresholds['memory'] ?? 50);
        $diskBelow = $disk < ($thresholds['disk'] ?? 50);

        if ($cpuBelow && $memoryBelow && $diskBelow) {
            // Ruaj në cache që threshold është nën
            $key = 'scale_down_triggered';
            $triggered = Cache::get($key, false);
            $firstTriggered = Cache::get("{$key}_time", null);

            if (!$triggered) {
                // Së pari që metrikat janë nën threshold
                Cache::put($key, true, now()->addSeconds($duration));
                Cache::put("{$key}_time", now(), now()->addSeconds($duration));
                return null; // Prit deri sa të kalojë koha e nevojshme
            }

            // Kontrollo nëse ka kaluar koha e nevojshme
            if ($firstTriggered && now()->diffInSeconds($firstTriggered) >= $duration) {
            // Kontrollo nëse ka më shumë se 1 resurs
            $resources = $this->resourceService->getResources() ?? [];
            $autoScaledResources = array_filter($resources, function ($resource) {
                return isset($resource['name']) && str_starts_with($resource['name'], 'auto-scaled-');
            });

                if (count($autoScaledResources) <= config('autoscaler.scaling.min_resources', 1)) {
                    // Reset cache dhe mos bëj scale down
                    Cache::forget('scale_down_triggered');
                    Cache::forget('scale_down_triggered_time');
                    return null;
                }

                // Reset cache
                Cache::forget('scale_down_triggered');
                Cache::forget('scale_down_triggered_time');

                return [
                    'action' => 'scale_down',
                    'reason' => "Të gjitha metrikat janë nën threshold për {$duration} sekonda",
                    'metrics' => [
                        'cpu' => $cpu,
                        'memory' => $memory,
                        'disk' => $disk,
                    ],
                    'resources_count' => count($autoScaledResources),
                ];
            }
        } else {
            // Reset nëse metrikat nuk janë më nën threshold
            Cache::forget('scale_down_triggered');
            Cache::forget('scale_down_triggered_time');
        }

        return null;
    }

    /**
     * Ekzekuton scale up
     *
     * @return bool
     */
    public function executeScaleUp(): bool
    {
        $scalingConfig = config('autoscaler.scaling.scale_up_resource');
        $resources = $this->resourceService->getResources() ?? [];
        
        // Kontrollo nëse ka arritur limitin maksimal
        $autoScaledResources = array_filter($resources, function ($resource) {
            return isset($resource['name']) && str_starts_with($resource['name'], 'auto-scaled-');
        });

        if (count($autoScaledResources) >= config('autoscaler.scaling.max_resources', 10)) {
            Log::warning("Autoscaler: Scale up i refuzuar - limiti maksimal i arritur", [
                'current' => count($autoScaledResources),
                'max' => config('autoscaler.scaling.max_resources', 10),
            ]);
            return false;
        }

        // Krijo resurs të ri
        $resourceData = [
            'name' => $scalingConfig['name_prefix'] . now()->format('YmdHis'),
            'type' => $scalingConfig['type'] ?? 'vm',
            'status' => $scalingConfig['status'] ?? 'active',
            'cpu_capacity' => $scalingConfig['cpu_capacity'] ?? 5,
            'memory_capacity' => $scalingConfig['memory_capacity'] ?? 500,
            'storage_capacity' => $scalingConfig['storage_capacity'] ?? 10,
            'region' => $scalingConfig['region'] ?? 'auto',
        ];

        $resource = $this->resourceService->createResource($resourceData);

        if ($resource) {
            // Ruaj në historik
            ScalingHistory::create([
                'action' => 'scale_up',
                'resource_id' => $resource['id'] ?? null,
                'resource_name' => $resourceData['name'],
                'metrics' => json_encode($this->monitoringService->getMetrics() ?? []),
                'reason' => 'Auto-scaling triggered by high system load',
            ]);

            Log::info("Autoscaler: Scale up u ekzekutua me sukses", [
                'resource_id' => $resource['id'] ?? null,
                'resource_name' => $resourceData['name'],
            ]);

            return true;
        }

        return false;
    }

    /**
     * Ekzekuton scale down
     *
     * @return bool
     */
    public function executeScaleDown(): bool
    {
        $resources = $this->resourceService->getResources() ?? [];
        $autoScaledResources = array_filter($resources, function ($resource) {
            return isset($resource['name']) && str_starts_with($resource['name'], 'auto-scaled-');
        });

        if (count($autoScaledResources) <= config('autoscaler.scaling.min_resources', 1)) {
            Log::warning("Autoscaler: Scale down i refuzuar - minimumi i arritur", [
                'current' => count($autoScaledResources),
                'min' => config('autoscaler.scaling.min_resources', 1),
            ]);
            return false;
        }

        // Gjej resursin më pak të përdorur (ose më të vjetër)
        usort($autoScaledResources, function ($a, $b) {
            $aTime = $a['created_at'] ?? $a['id'] ?? 0;
            $bTime = $b['created_at'] ?? $b['id'] ?? 0;
            return $aTime <=> $bTime;
        });

        $resourceToDelete = reset($autoScaledResources);
        $resourceId = $resourceToDelete['id'] ?? null;

        if (!$resourceId) {
            return false;
        }

        $deleted = $this->resourceService->deleteResource($resourceId);

        if ($deleted) {
            // Ruaj në historik
            ScalingHistory::create([
                'action' => 'scale_down',
                'resource_id' => $resourceId,
                'resource_name' => $resourceToDelete['name'] ?? 'unknown',
                'metrics' => json_encode($this->monitoringService->getMetrics() ?? []),
                'reason' => 'Auto-scaling triggered by low system load',
            ]);

            Log::info("Autoscaler: Scale down u ekzekutua me sukses", [
                'resource_id' => $resourceId,
                'resource_name' => $resourceToDelete['name'] ?? 'unknown',
            ]);

            return true;
        }

        return false;
    }
}

