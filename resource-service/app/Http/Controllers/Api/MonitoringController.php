<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\JsonResponse;

/**
 * MonitoringController - API Controller për komunikimin me monitoring-service
 * 
 * Ky controller demonstron si resource-service komunikon me monitoring-service
 * dhe ofron endpoint-e për të marrë informacion rreth statusit të komunikimit.
 */
class MonitoringController extends Controller
{
    protected $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Merr statusin e komunikimit me monitoring-service
     * 
     * @return JsonResponse
     */
    public function getServiceStatus(): JsonResponse
    {
        $status = $this->monitoringService->getServiceStatus();

        return response()->json([
            'success' => true,
            'data' => $status,
            'message' => 'Statusi i monitoring-service u mor me sukses'
        ]);
    }

    /**
     * Merr metrikat e sistemit nga monitoring-service
     * 
     * @return JsonResponse
     */
    public function getMetrics(): JsonResponse
    {
        $metrics = $this->monitoringService->getSystemMetrics();

        if ($metrics) {
            return response()->json([
                'success' => true,
                'data' => $metrics,
                'message' => 'Metrikat u morën me sukses'
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => null,
            'message' => 'Nuk u arrit lidhja me monitoring-service'
        ], 503);
    }

    /**
     * Verifikon kapacitetin para krijimit të resursit
     * 
     * @return JsonResponse
     */
    public function checkCapacity(): JsonResponse
    {
        $requestedCpu = request()->input('cpu', 0);
        $requestedMemory = request()->input('memory', 0);
        $requestedStorage = request()->input('storage', 0);

        $result = $this->monitoringService->checkCapacityAvailability(
            $requestedCpu,
            $requestedMemory,
            $requestedStorage
        );

        return response()->json([
            'success' => $result['available'],
            'data' => [
                'available' => $result['available'],
                'message' => $result['message'],
                'metrics' => $result['metrics']
            ]
        ], $result['available'] ? 200 : 400);
    }
}

