<?php

namespace App\Services\Enterprise;

use Illuminate\Support\Facades\Log;

final class StructuredLogService
{
    public function auth(string $message, array $context = []): void
    {
        Log::channel('auth')->info($message, $context);
    }

    public function payment(string $message, array $context = []): void
    {
        Log::channel('payments')->info($message, $context);
    }

    public function order(string $message, array $context = []): void
    {
        Log::channel('orders')->info($message, $context);
    }

    public function ai(string $message, array $context = []): void
    {
        Log::channel('ai')->info($message, $context);
    }

    public function security(string $message, array $context = []): void
    {
        Log::channel('security')->warning($message, $context);
    }

    public function queue(string $message, array $context = []): void
    {
        Log::channel('queues')->info($message, $context);
    }
}
