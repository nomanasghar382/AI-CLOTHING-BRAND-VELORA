<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class LogApiPerformance
{
    public function handle(Request $request, Closure $next): Response
    {
        $started = microtime(true);
        $response = $next($request);
        $duration = round((microtime(true) - $started) * 1000, 2);

        if ($request->is('api/*') && $duration >= (int) config('velora.performance.slow_request_ms', 750)) {
            Log::warning('velora.slow_request', [
                'method' => $request->method(),
                'path' => $request->path(),
                'duration_ms' => $duration,
                'status' => $response->getStatusCode(),
            ]);
        }

        $response->headers->set('X-Response-Time', "{$duration}ms");

        return $response;
    }
}
