<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ValidateUpload
{
    public function handle(Request $request, Closure $next): Response
    {
        foreach ($request->allFiles() as $file) {
            $files = is_array($file) ? $file : [$file];
            foreach ($files as $upload) {
                if (! $upload) {
                    continue;
                }
                $maxKb = (int) config('velora.security.upload_max_kb', 10240);
                if ($upload->getSize() > $maxKb * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Uploaded file exceeds the allowed size.',
                        'data' => null,
                        'errors' => ['file' => ['Upload too large.']],
                        'status' => 422,
                        'timestamp' => now()->toIso8601String(),
                    ], 422);
                }
            }
        }

        return $next($request);
    }
}
