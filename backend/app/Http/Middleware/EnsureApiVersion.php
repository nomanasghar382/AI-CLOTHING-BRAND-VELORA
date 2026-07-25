<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureApiVersion
{
    public function handle(Request $request, Closure $next, string $version = 'v1'): Response
    {
        $requested = $request->header('X-Api-Version', config('velora.api.default_version', 'v1'));

        if (! in_array($requested, config('velora.api.supported_versions', ['v1']), true)) {
            return response()->json([
                'success' => false,
                'message' => "API version [{$requested}] is not supported.",
                'data' => ['supported_versions' => config('velora.api.supported_versions', ['v1'])],
                'errors' => [],
                'status' => 400,
                'timestamp' => now()->toIso8601String(),
            ], 400);
        }

        if (in_array($requested, config('velora.api.deprecated_versions', []), true)) {
            $response = $next($request);
            $response->headers->set('Deprecation', 'true');
            $response->headers->set('Sunset', now()->addMonths(6)->toRfc7231String());

            return $response;
        }

        $response = $next($request);
        $response->headers->set('X-Api-Version', $version);

        return $response;
    }
}
