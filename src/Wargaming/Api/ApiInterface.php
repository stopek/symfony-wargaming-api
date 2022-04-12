<?php

namespace App\Wargaming\Api;

use App\Wargaming\ApiResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface ApiInterface
{
    public function createResponse(string $path, array $data = []): ?ResponseInterface;

    public function get(string $path, array $data = [], array $post = []): ApiResponse;

    /** @return ApiResponse[] */
    public function multiple(string $path, array $data = []): array;
}