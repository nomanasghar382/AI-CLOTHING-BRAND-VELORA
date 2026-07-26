<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

final class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Request completed successfully.', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'status' => $status,
            'timestamp' => Carbon::now()->toIso8601String(),
        ], $status);
    }

    public static function error(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => (object) $errors,
            'status' => $status,
            'timestamp' => Carbon::now()->toIso8601String(),
        ], $status);
    }
}
