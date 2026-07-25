<?php

namespace App\Jobs;

use App\Services\Enterprise\CacheManagerService;
use App\Services\Style\RecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class RefreshRecommendationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function handle(CacheManagerService $cache): void
    {
        $cache->flushTag('recommendations');
    }
}
