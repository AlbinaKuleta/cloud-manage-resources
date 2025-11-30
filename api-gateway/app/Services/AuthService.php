<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * AuthService - Komunikim me auth-service për validimin e token-ave
 */
class AuthService
{
    private $authServiceUrl;
    private $timeout;
    private $cacheTtl;

    public function __construct()
    {
        $this->authServiceUrl = config('gateway.auth.service_url', 'http://127.0.0.1:8003');
        $this->timeout = config('gateway.auth.timeout', 5);
        $this->cacheTtl = config('gateway.auth.cache_ttl', 60);
    }

    /**
     * Validon token-in dhe kthen informacionin e përdoruesit
     *
     * @param string $token
     * @return array|null
     */
    public function validateToken(string $token): ?array
    {
        // Cache për të reduktuar kërkesat
        $cacheKey = "auth_token_{$token}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($token) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withToken($token)
                    ->get("{$this->authServiceUrl}/api/me");

                if ($response->successful()) {
                    $data = $response->json();
                    
                    Log::info("Gateway: Token validated", [
                        'user_id' => $data['user']['id'] ?? null,
                        'user_email' => $data['user']['email'] ?? null,
                    ]);

                    return $data['user'] ?? null;
                } else {
                    Log::warning("Gateway: Token validation failed", [
                        'status' => $response->status(),
                    ]);

                    return null;
                }
            } catch (\Exception $e) {
                Log::error("Gateway: Error validating token", [
                    'error' => $e->getMessage(),
                ]);

                return null;
            }
        });
    }

    /**
     * Kontrollon nëse përdoruesi ka role të caktuar
     *
     * @param string $token
     * @param string $requiredRole
     * @return bool
     */
    public function hasRole(string $token, string $requiredRole): bool
    {
        $user = $this->validateToken($token);
        
        if (!$user) {
            return false;
        }

        $userRole = $user['role'] ?? 'viewer';
        
        // Admin ka akses në të gjitha
        if ($userRole === 'admin') {
            return true;
        }

        return $userRole === $requiredRole;
    }

    /**
     * Merr informacionin e përdoruesit nga token
     *
     * @param string $token
     * @return array|null
     */
    public function getUser(string $token): ?array
    {
        return $this->validateToken($token);
    }
}

