<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RouteService;
use Illuminate\Support\Facades\Log;

/**
 * GatewayController - Menaxhon të gjitha kërkesat përmes API Gateway
 */
class GatewayController extends Controller
{
    private $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    /**
     * Rutejë kërkesat për auth-service
     */
    public function auth(Request $request, $path = '')
    {
        return $this->routeToService($request, 'auth', $path);
    }

    /**
     * Rutejë kërkesat për resource-service
     */
    public function resources(Request $request, $path = '')
    {
        return $this->routeToService($request, 'resource', $path);
    }

    /**
     * Rutejë kërkesat për monitoring-service
     */
    public function monitoring(Request $request, $path = '')
    {
        return $this->routeToService($request, 'monitoring', $path);
    }

    /**
     * Rutejë kërkesat për autoscaler-service
     */
    public function autoscaler(Request $request, $path = '')
    {
        return $this->routeToService($request, 'autoscaler', $path);
    }

    /**
     * Rutejë kërkesën në servisin e duhur
     *
     * @param Request $request
     * @param string $serviceName
     * @param string $path
     * @return \Illuminate\Http\JsonResponse
     */
    private function routeToService(Request $request, string $serviceName, string $path = '')
    {
        // Kontrollo nëse service është i disponueshëm
        if (!$this->routeService->isServiceAvailable($serviceName)) {
            Log::error("Gateway: Service unavailable", [
                'service' => $serviceName,
            ]);

            return response()->json([
                'error' => 'Service Unavailable',
                'message' => "The {$serviceName} service is currently unavailable. Please try again later.",
                'service' => $serviceName,
            ], 503);
        }

        // Rutejë kërkesën
        $response = $this->routeService->route($request, $serviceName);

        if (!$response) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to route request to service',
                'service' => $serviceName,
            ], 500);
        }

        // Kthen përgjigjen
        $statusCode = $response->status();
        $data = $response->json();

        return response()->json($data, $statusCode, $response->headers->all());
    }

    /**
     * Health check endpoint
     */
    public function health()
    {
        $services = ['auth', 'resource', 'monitoring', 'autoscaler'];
        $status = [];

        foreach ($services as $service) {
            $status[$service] = [
                'available' => $this->routeService->isServiceAvailable($service),
                'url' => config("gateway.services.{$service}.url"),
            ];
        }

        return response()->json([
            'status' => 'ok',
            'gateway' => 'operational',
            'services' => $status,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

