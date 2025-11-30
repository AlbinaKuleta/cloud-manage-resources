<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * RouteService - Menaxhon routing e kërkesave në serviset e duhura
 */
class RouteService
{
    /**
     * Rutejë kërkesën në servisin e duhur
     *
     * @param Request $request
     * @param string $serviceName
     * @return \Illuminate\Http\Client\Response|null
     */
    public function route(Request $request, string $serviceName)
    {
        $serviceConfig = config("gateway.services.{$serviceName}");
        
        if (!$serviceConfig) {
            Log::error("Gateway: Service '{$serviceName}' nuk u gjet në konfigurim");
            return null;
        }

        $serviceUrl = $serviceConfig['url'];
        $timeout = $serviceConfig['timeout'] ?? 5;
        $prefix = $serviceConfig['prefix'] ?? '';

        // Merr path-in pas prefix-it
        $path = $request->path();
        $path = str_replace($prefix, '', $path);
        $path = ltrim($path, '/');
        
        // Ndërto URL-në e plotë
        $fullUrl = rtrim($serviceUrl, '/') . '/' . $path;
        
        // Shto query parameters nëse ka
        if ($request->query()) {
            $fullUrl .= '?' . http_build_query($request->query());
        }

        // Merr metodën HTTP
        $method = strtolower($request->method());

        // Përgatit headers
        $headers = $this->prepareHeaders($request);

        // Merr body për POST/PUT/PATCH
        $data = [];
        if (in_array($method, ['post', 'put', 'patch'])) {
            $data = $request->all();
        }

        try {
            Log::info("Gateway: Routing request", [
                'service' => $serviceName,
                'method' => strtoupper($method),
                'url' => $fullUrl,
                'path' => $path,
            ]);

            // Bëj kërkesën
            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->{strtolower($method)}($fullUrl, $data);

            Log::info("Gateway: Response received", [
                'service' => $serviceName,
                'status' => $response->status(),
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error("Gateway: Error routing request", [
                'service' => $serviceName,
                'url' => $fullUrl,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Përgatit headers për kërkesën
     *
     * @param Request $request
     * @return array
     */
    private function prepareHeaders(Request $request): array
    {
        $headers = [];

        // Kopjo headers të rëndësishëm
        $importantHeaders = [
            'Authorization',
            'Content-Type',
            'Accept',
            'X-Requested-With',
        ];

        foreach ($importantHeaders as $header) {
            if ($request->hasHeader($header)) {
                $headers[$header] = $request->header($header);
            }
        }

        // Shto token nëse ekziston në session
        if (session()->has('token')) {
            $headers['Authorization'] = 'Bearer ' . session('token');
        }

        return $headers;
    }

    /**
     * Kontrollon nëse një service është i disponueshëm
     *
     * @param string $serviceName
     * @return bool
     */
    public function isServiceAvailable(string $serviceName): bool
    {
        $serviceConfig = config("gateway.services.{$serviceName}");
        
        if (!$serviceConfig) {
            return false;
        }

        try {
            $response = Http::timeout(2)
                ->get(rtrim($serviceConfig['url'], '/') . '/up');

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}

