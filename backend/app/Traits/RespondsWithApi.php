<?php

namespace App\Traits;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

trait RespondsWithApi
{
    protected function success(mixed $data = null, string $message = 'Request completed successfully.', int $status = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $status);
    }

    protected function failure(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        return ApiResponse::error($message, $errors, $status);
    }
}
