<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ResourceService - Komunikim me resource-service
 */
class ResourceService
{
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('autoscaler.resource.service_url', 'http://127.0.0.1:8001');
        $this->timeout = config('autoscaler.resource.timeout', 5);
    }

    /**
     * Merr të gjitha resurset
     *
     * @return array|null
     */
    public function getResources(): ?array
    {
        try {
            // Për komunikim me resource-service, duhet token
            // Për autoscaler, do të përdorim një token të veçantë ose do ta bëjmë pa auth
            // Për tani, do të provojmë pa token (nëse resource-service e lejon)
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/resources");

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info("Autoscaler: Resurset u morën", [
                    'count' => count($data['data'] ?? []),
                ]);

                return $data['data'] ?? [];
            } else {
                Log::warning("Autoscaler: Dështoi marrja e resurseve", [
                    'status' => $response->status(),
                ]);

                return null;
            }
        } catch (\Exception $e) {
            Log::error("Autoscaler: Gabim në marrjen e resurseve", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Krijon një resurs të ri
     *
     * @param array $resourceData
     * @return array|null
     */
    public function createResource(array $resourceData): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/resources", $resourceData);

            if ($response->successful()) {
                $resource = $response->json();
                
                Log::info("Autoscaler: Resursi u krijua", [
                    'resource_id' => $resource['id'] ?? null,
                    'name' => $resourceData['name'] ?? null,
                ]);

                return $resource;
            } else {
                Log::warning("Autoscaler: Dështoi krijimi i resursit", [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return null;
            }
        } catch (\Exception $e) {
            Log::error("Autoscaler: Gabim në krijimin e resursit", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fshin një resurs
     *
     * @param int $resourceId
     * @return bool
     */
    public function deleteResource(int $resourceId): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->delete("{$this->baseUrl}/api/resources/{$resourceId}");

            if ($response->successful()) {
                Log::info("Autoscaler: Resursi u fshi", [
                    'resource_id' => $resourceId,
                ]);

                return true;
            } else {
                Log::warning("Autoscaler: Dështoi fshirja e resursit", [
                    'resource_id' => $resourceId,
                    'status' => $response->status(),
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error("Autoscaler: Gabim në fshirjen e resursit", [
                'resource_id' => $resourceId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

