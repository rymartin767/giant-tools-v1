<?php

namespace App\Contracts;

use Illuminate\Http\Client\Response;

interface ServiceInterface
{
    public function setToken(): bool;

    public function isAuthenticated(): bool;

    public function fetch(string $endpoint, array $filters): array;

    // ! INTERNAL METHODS - SHOULD NOT BE USED OUTSIDE OF THE SERVICE
    public function handleSuccess(Response $response): array;

    public function handleFailure(Response $response): array;

    public function createErrorResponse(int $code, string $message): array;
}
