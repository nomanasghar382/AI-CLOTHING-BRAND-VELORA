<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

final class SystemHealthController extends Controller
{
  use RespondsWithApi;

  public function index(): JsonResponse
  {
    $queueSize = DB::table('jobs')->count();
    $failedJobs = DB::table('failed_jobs')->count();

    return $this->success([
      'application' => [
        'environment' => app()->environment(),
        'debug' => (bool) config('app.debug'),
        'laravel' => app()->version(),
      ],
      'database' => ['connected' => $this->databaseHealthy()],
      'cache' => [
        'driver' => config('cache.default'),
        'healthy' => $this->cacheHealthy(),
      ],
      'queue' => [
        'connection' => config('queue.default'),
        'pending_jobs' => $queueSize,
        'failed_jobs' => $failedJobs,
      ],
      'storage' => [
        'public_writable' => is_writable(storage_path('app/public')),
        'logs_writable' => is_writable(storage_path('logs')),
        'disk_free_mb' => round(disk_free_space(storage_path()) / 1024 / 1024, 2),
      ],
    ]);
  }

  public function clearCache(): JsonResponse
  {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return $this->success(null, 'Application caches cleared.');
  }

  private function databaseHealthy(): bool
  {
    try {
      DB::connection()->getPdo();

      return true;
    } catch (\Throwable) {
      return false;
    }
  }

  private function cacheHealthy(): bool
  {
    try {
      Cache::put('velora.health', 'ok', 30);

      return Cache::get('velora.health') === 'ok';
    } catch (\Throwable) {
      return false;
    }
  }
}
