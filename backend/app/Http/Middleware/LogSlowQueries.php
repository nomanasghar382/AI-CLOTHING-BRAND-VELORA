<?php

namespace App\Http\Middleware;

use App\Services\Enterprise\MetricsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class LogSlowQueries
{
    public function handle(Request $request, Closure $next): Response
    {
        $threshold = (int) config('velora.observability.slow_query_ms', 500);
        DB::listen(function ($query) use ($threshold, $request): void {
            if ($query->time >= $threshold) {
                Log::channel('performance')->warning('slow_query', [
                    'sql' => $query->sql,
                    'time_ms' => $query->time,
                    'path' => $request->path(),
                ]);
                app(MetricsService::class)->record('slow_query', (float) $query->time, 'database', [
                    'path' => $request->path(),
                ]);
            }
        });

        return $next($request);
    }
}
