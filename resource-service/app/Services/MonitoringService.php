<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\MetricsHistory;

/**
 * MonitoringService - Komunikimi me monitoring-service
 * 
 * Ky servis menaxhon të gjitha komunikimet me monitoring-service
 * dhe demonstron komunikimin midis mikrosherbimeve.
 */
class MonitoringService
{
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.monitoring.url', 'http://monitoring-service:8002');
        $this->timeout = config('services.monitoring.timeout', 5);
    }

    /**
     * Merr metrikat globale të sistemit nga monitoring-service
     * 
     * @return array|null
     */
    public function getSystemMetrics()
    {
        try {
            // Cache për 5 sekonda për të reduktuar kërkesat
            return Cache::remember('system_metrics', 5, function () {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/api/metrics");

                if ($response->successful()) {
                    $metrics = $response->json();
                    
                    // Ruaj në historik për grafikët
                    MetricsHistory::create([
                        'cpu_usage' => $metrics['cpu_usage'] ?? 0,
                        'memory_usage' => $metrics['memory_usage'] ?? 0,
                        'disk_usage' => $metrics['disk_usage'] ?? 0,
                        'recorded_at' => now(),
                    ]);
                    
                    // Fshi të dhënat e vjetra (më shumë se 100 rekorde)
                    MetricsHistory::where('recorded_at', '<', now()->subHours(24))->delete();
                    
                    // Log komunikimin e suksesshëm
                    Log::info('Monitoring Service: Metrikat u morën me sukses', [
                        'service' => 'monitoring-service',
                        'endpoint' => '/api/metrics',
                        'metrics' => $metrics
                    ]);

                    return $metrics;
                } else {
                    Log::warning('Monitoring Service: Dështoi marrja e metrikave', [
                        'service' => 'monitoring-service',
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]);

                    return null;
                }
            });
        } catch (\Exception $e) {
            Log::error('Monitoring Service: Gabim në komunikim', [
                'service' => 'monitoring-service',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Verifikon nëse sistemi ka kapacitet të mjaftueshëm për një resurs të ri
     * 
     * @param int $requestedCpu CPU e kërkuar në %
     * @param int $requestedMemory Memory e kërkuar në MB
     * @param int $requestedStorage Storage i kërkuar në GB
     * @return array ['available' => bool, 'message' => string, 'metrics' => array]
     */
    public function checkCapacityAvailability($requestedCpu, $requestedMemory, $requestedStorage)
    {
        try {
            $metrics = $this->getSystemMetrics();

            if (!$metrics) {
                return [
                    'available' => false,
                    'message' => 'Nuk u arrit lidhja me Monitoring-Service. Verifikoni lidhjen.',
                    'metrics' => null
                ];
            }

            // Kontrollon nëse sistemi është shumë i ngarkuar
            $isOverloaded = $metrics['cpu_usage'] > 85 || 
                           $metrics['memory_usage'] > 90 || 
                           $metrics['disk_usage'] > 95;

            if ($isOverloaded) {
                Log::warning('Monitoring Service: Sistemi është shumë i ngarkuar', [
                    'cpu_usage' => $metrics['cpu_usage'],
                    'memory_usage' => $metrics['memory_usage'],
                    'disk_usage' => $metrics['disk_usage']
                ]);

                return [
                    'available' => false,
                    'message' => 'Sistemi është shumë i ngarkuar për krijimin e resurseve. Provoni më vonë!',
                    'metrics' => $metrics
                ];
            }

            // Log verifikimin e suksesshëm
            Log::info('Monitoring Service: Kapaciteti u verifikua', [
                'requested_cpu' => $requestedCpu,
                'requested_memory' => $requestedMemory,
                'requested_storage' => $requestedStorage,
                'system_cpu' => $metrics['cpu_usage'],
                'system_memory' => $metrics['memory_usage'],
                'system_disk' => $metrics['disk_usage']
            ]);

            return [
                'available' => true,
                'message' => 'Kapaciteti është i disponueshëm',
                'metrics' => $metrics
            ];
        } catch (\Exception $e) {
            Log::error('Monitoring Service: Gabim në verifikimin e kapacitetit', [
                'error' => $e->getMessage()
            ]);

            return [
                'available' => false,
                'message' => 'Gabim në verifikimin e kapacitetit: ' . $e->getMessage(),
                'metrics' => null
            ];
        }
    }

    /**
     * Kontrollon nëse monitoring-service është i disponueshëm
     * 
     * @return bool
     */
    public function isAvailable()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/metrics");

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Monitoring Service: Nuk u arrit lidhja', [
                'url' => $this->baseUrl,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Merr statusin e monitoring-service
     * 
     * @return array
     */
    public function getServiceStatus()
    {
        $isAvailable = $this->isAvailable();
        $metrics = $isAvailable ? $this->getSystemMetrics() : null;

        return [
            'available' => $isAvailable,
            'url' => $this->baseUrl,
            'last_metrics' => $metrics,
            'timestamp' => now()->toIso8601String()
        ];
    }
}

