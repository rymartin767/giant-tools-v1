<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AviationWeatherService implements ServiceInterface
{
    private const BASE_URL = 'https://aviationweather.gov/api/data';

    private const SERVICE_NAME = 'aviationweather';

    public function setToken(): bool
    {
        // No authentication required for Aviation Weather API
        return true;
    }

    public function isAuthenticated(): bool
    {
        // Public API, always authenticated
        return true;
    }

    public function fetch(string $endpoint, array $filters = []): array
    {
        $fullUrl = self::BASE_URL.'/'.ltrim($endpoint, '/');

        Log::info('Aviation Weather API request', [
            'endpoint' => $endpoint,
            'filters' => $filters,
            'full_url' => $fullUrl,
        ]);

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'GiantTools/1.0 (Laravel)',
                ])
                ->get($fullUrl, $filters);

            Log::info('Aviation Weather API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            // Handle 204 No Content (no data available)
            if ($response->status() === 204) {
                return [
                    'success' => true,
                    'data' => [],
                ];
            }

            return $response->successful()
                ? $this->handleSuccess($response)
                : $this->handleFailure($response);

        } catch (ConnectionException $e) {
            Log::error('Aviation Weather API connection failed', [
                'endpoint' => $endpoint,
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            return $this->createErrorResponse(503, 'Weather service temporarily unavailable');
        } catch (\Exception $e) {
            Log::error('Aviation Weather API request failed', [
                'endpoint' => $endpoint,
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            return $this->createErrorResponse(500, 'Internal service error');
        }
    }

    public function getMetar(string $icao): array
    {
        return $this->fetch('metar', [
            'ids' => strtoupper($icao),
            'format' => 'json',
        ]);
    }

    public function getTaf(string $icao): array
    {
        return $this->fetch('taf', [
            'ids' => strtoupper($icao),
            'format' => 'json',
        ]);
    }

    public function parseFlightCategory(?string $category): array
    {
        if (empty($category)) {
            return [
                'label' => 'N/A',
                'color' => 'gray',
            ];
        }

        return match (strtoupper($category)) {
            'VFR' => [
                'label' => 'VFR',
                'color' => 'green',
            ],
            'MVFR' => [
                'label' => 'MVFR',
                'color' => 'blue',
            ],
            'IFR' => [
                'label' => 'IFR',
                'color' => 'red',
            ],
            'LIFR' => [
                'label' => 'LIFR',
                'color' => 'purple',
            ],
            default => [
                'label' => $category,
                'color' => 'gray',
            ],
        };
    }

    // * HANDLE SUCCESS
    public function handleSuccess(Response $response): array
    {
        $data = $response->json();

        return [
            'success' => true,
            'data' => $data,
        ];
    }

    // ! HANDLE ERRORS
    public function handleFailure(Response $response): array
    {
        $jsonResponse = $response->json();

        $message = $jsonResponse['error']
            ?? $jsonResponse['message']
            ?? 'Request failed with status '.$response->status();

        Log::warning('Aviation Weather API request failed', [
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
                'service' => 'Aviation Weather Service',
                'code' => $code,
                'message' => $message,
            ],
        ];
    }
}
