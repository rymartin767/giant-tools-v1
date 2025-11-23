<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class GiantsService implements ServiceInterface
{
    private const BASE_URL = 'https://api.giantpilots.com/api/v1';

    private const SERVICE_NAME = 'giantpilots';

    private ?string $token = null;

    public function __construct()
    {
        // Auto-set token on instantiation for convenience
        $this->setToken();
    }

    public function setToken(): bool
    {
        // Get token from config (no longer requires authentication)
        $this->token = config('services.giant_api.token');

        if (! $this->token) {
            Log::warning('Giants API token not configured');

            return false;
        }

        return true;
    }

    public function isAuthenticated(): bool
    {
        if (empty($this->token)) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->get(self::BASE_URL.'/auth/verify');

            return $response->successful();
        } catch (ConnectionException $e) {
            Log::warning('Giants authentication check failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function fetch(string $endpoint, array $filters = []): array
    {
        if (empty($this->token)) {
            return $this->createErrorResponse(401, 'No authentication token available');
        }

        $fullUrl = self::BASE_URL.'/'.ltrim($endpoint, '/');

        Log::info('Giants API request', [
            'endpoint' => $endpoint,
            'filters' => $filters,
            'full_url' => $fullUrl,
            'token_available' => ! empty($this->token),
        ]);

        try {
            $response = Http::timeout(30)
                ->withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->retry(3, 1000) // Retry 3 times with 1 second delay
                ->get($fullUrl, $filters);

            Log::info('Giants API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            // If we get a 401, the token is invalid
            if ($response->status() === 401) {
                return $this->createErrorResponse(401, 'Authentication token is invalid');
            }

            return $response->successful()
                ? $this->handleSuccess($response)
                : $this->handleFailure($response);

        } catch (ConnectionException $e) {
            Log::error('Giants API connection failed', [
                'endpoint' => $endpoint,
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            return $this->createErrorResponse(503, 'Service temporarily unavailable');
        } catch (\Exception $e) {
            Log::error('Giants API request failed', [
                'endpoint' => $endpoint,
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            return $this->createErrorResponse(500, 'Internal service error');
        }
    }

    // * HANDLE SUCCESS
    public function handleSuccess(Response $response): array
    {
        $data = $response->json();

        // Handle different response structures gracefully
        return [
            'success' => true,
            'data' => $data['data'] ?? $data,
        ];
    }

    // ! HANDLE ERRORS
    public function handleFailure(Response $response): array
    {
        $jsonResponse = $response->json();

        $message = $jsonResponse['detail']
            ?? $jsonResponse['message']
            ?? $jsonResponse['error']
            ?? 'Request failed with status '.$response->status();

        Log::warning('Giants API request failed', [
            'status' => $response->status(),
            'response' => $jsonResponse,
        ]);

        return $this->createErrorResponse($response->status(), $message);
    }

    public function createErrorResponse(int $code, string $message): array
    {
        return [
            'success' => false,
            'error' => [
                'service' => 'Giants Service',
                'code' => $code,
                'message' => $message,
            ],
        ];
    }
}
