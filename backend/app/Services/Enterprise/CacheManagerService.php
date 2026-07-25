<?php

namespace App\Services\Enterprise;

use Illuminate\Support\Facades\Cache;

final class CacheManagerService
{
    public function remember(string $tag, string $key, callable $callback, ?int $ttl = null): mixed
    {
        $ttl ??= (int) config("velora.cache.ttl.{$tag}", 600);
        $store = Cache::getStore();

        if (method_exists($store, 'tags')) {
            return Cache::tags([config("velora.cache.tags.{$tag}", "velora:{$tag}")])->remember($key, $ttl, $callback);
        }

        return Cache::remember("{$tag}:{$key}", $ttl, $callback);
    }

    public function flushTag(string $tag): void
    {
        $store = Cache::getStore();
        if (method_exists($store, 'tags')) {
            Cache::tags([config("velora.cache.tags.{$tag}", "velora:{$tag}")])->flush();
        }
    }

    public function flushAll(): void
    {
        foreach (array_keys(config('velora.cache.tags', [])) as $tag) {
            $this->flushTag($tag);
        }
        Cache::flush();
    }

    public function warmup(): array
    {
        $warmed = [];
        foreach (['catalog', 'search', 'dashboard', 'config'] as $tag) {
            $key = "{$tag}:warmup";
            $this->remember($tag, $key, fn () => now()->toIso8601String(), 60);
            $warmed[] = $tag;
        }

        return $warmed;
    }
}
