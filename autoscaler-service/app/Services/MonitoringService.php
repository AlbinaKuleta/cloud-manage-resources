<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * MonitoringService - Komunikim me monitoring-service
 */
class MonitoringService
{
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('autoscaler.monitoring.service_url', 'http://127.0.0.1:8002');
        $this->timeout = config('autoscaler.monitoring.timeout', 5);
    }

    /**
     * Merr metrikat aktuale nga monitoring-service
     *
     * @return array|null
     */
    public function getMetrics(): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/metrics");

            if ($response->successful()) {
                $metrics = $response->json();
                
                Log::info("Autoscaler: Metrikat u morën", [
                    'cpu' => $metrics['cpu_usage'] ?? 0,
                    'memory' => $metrics['memory_usage'] ?? 0,
                    'disk' => $metrics['disk_usage'] ?? 0,
                ]);

                return $metrics;
            } else {
                Log::warning("Autoscaler: Dështoi marrja e metrikave", [
                    'status' => $response->status(),
                ]);

                return null;
            }
        } catch (\Exception $e) {
            Log::error("Autoscaler: Gabim në marrjen e metrikave", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Kontrollon nëse monitoring-service është i disponueshëm
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(2)
                ->get("{$this->baseUrl}/api/metrics");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}

