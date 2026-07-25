<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Enterprise\CacheManagerService;
use App\Services\Enterprise\MetricsService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

final class SystemHealthController extends Controller
{
  use RespondsWithApi;

  public function __construct(
    private readonly MetricsService $metrics,
    private readonly CacheManagerService $cache,
  ) {
  }

  public function index(): JsonResponse
  {
    return $this->success([
      'application' => [
        'environment' => app()->environment(),
        'debug' => (bool) config('app.debug'),
        'laravel' => app()->version(),
        'maintenance_mode' => app()->isDownForMaintenance(),
      ],
      'metrics' => $this->metrics->snapshot(),
    ]);
  }

  public function clearCache(): JsonResponse
  {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    $this->cache->flushAll();

    return $this->success(null, 'Application caches cleared.');
  }
}
