<?php

namespace App\Services\Catalog;

use App\Models\Product;
use App\Models\SearchAnalytics;
use App\Models\SearchHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class SearchService
{
  public function search(array $filters, ?int $userId, ?string $sessionId): LengthAwarePaginator
  {
    $query = trim((string) ($filters['q'] ?? ''));
    $perPage = min(max((int) ($filters['per_page'] ?? 12), 1), 48);

    if ($query !== '') {
      $this->recordSearch($query, $userId, $sessionId);
    }

    $builder = Product::query()
      ->with(['brand', 'category', 'images'])
      ->where('status', 'published');

    if ($query !== '') {
      $terms = $this->expandTerms($query);
      $builder->where(function ($q) use ($terms): void {
        foreach ($terms as $term) {
          $q->orWhere('name', 'like', "%{$term}%")
            ->orWhere('short_description', 'like', "%{$term}%")
            ->orWhere('fabric', 'like', "%{$term}%")
            ->orWhere('material', 'like', "%{$term}%");
        }
      });
    }

    foreach (['category', 'brand', 'fabric', 'material', 'coverage_level'] as $field) {
      if (!empty($filters[$field])) {
        if (in_array($field, ['category', 'brand'], true)) {
          $relation = $field;
          $builder->whereHas($relation, fn ($q) => $q->where('slug', $filters[$field]));
        } else {
          $builder->where($field, $filters[$field]);
        }
      }
    }

    if (!empty($filters['colors'])) {
      $colors = is_array($filters['colors']) ? $filters['colors'] : explode(',', (string) $filters['colors']);
      $builder->whereHas('colors', fn ($q) => $q->whereIn('slug', $colors));
    }

    if (!empty($filters['sizes'])) {
      $sizes = is_array($filters['sizes']) ? $filters['sizes'] : explode(',', (string) $filters['sizes']);
      $builder->whereHas('sizes', fn ($q) => $q->whereIn('slug', $sizes));
    }

    if (isset($filters['min_price'])) {
      $builder->where('price', '>=', (float) $filters['min_price']);
    }
    if (isset($filters['max_price'])) {
      $builder->where('price', '<=', (float) $filters['max_price']);
    }
    if (!empty($filters['in_stock'])) {
      $builder->where('stock_quantity', '>', 0);
    }

    $sort = (string) ($filters['sort'] ?? 'relevance');
    match ($sort) {
      'price_asc' => $builder->orderByRaw('COALESCE(sale_price, price)'),
      'price_desc' => $builder->orderByRaw('COALESCE(sale_price, price) DESC'),
      'newest' => $builder->latest('published_at'),
      'popular' => $builder->orderByDesc('views_count'),
      default => $query !== ''
        ? $builder->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ["%{$query}%"])->orderByDesc('views_count')
        : $builder->latest('published_at'),
    };

    return $builder->paginate($perPage)->withQueryString();
  }

  public function suggestions(string $term): array
  {
    $term = trim($term);
    if ($term === '') {
      return $this->popular();
    }

    return Cache::remember("search.suggest.{$term}", 300, function () use ($term) {
      $products = Product::query()
        ->where('status', 'published')
        ->where('name', 'like', "%{$term}%")
        ->limit(5)
        ->pluck('name')
        ->all();

      $analytics = SearchAnalytics::query()
        ->where('query', 'like', "%{$term}%")
        ->orderByDesc('search_count')
        ->limit(5)
        ->pluck('query')
        ->all();

      return array_values(array_unique(array_merge($products, $analytics)));
    });
  }

  public function popular(): array
  {
    return Cache::remember('search.popular', 600, fn () => SearchAnalytics::query()
      ->orderByDesc('search_count')
      ->limit(8)
      ->pluck('query')
      ->all());
  }

  public function recent(?int $userId, ?string $sessionId): array
  {
    return SearchHistory::query()
      ->when($userId, fn ($q) => $q->where('user_id', $userId), fn ($q) => $q->where('session_id', $sessionId))
      ->latest()
      ->limit(10)
      ->pluck('query')
      ->unique()
      ->values()
      ->all();
  }

  private function recordSearch(string $query, ?int $userId, ?string $sessionId): void
  {
    SearchHistory::query()->create([
      'user_id' => $userId,
      'session_id' => $sessionId,
      'query' => $query,
      'results_count' => 0,
      'source' => 'catalog',
    ]);

    SearchAnalytics::query()->updateOrCreate(
      ['query' => mb_strtolower($query)],
      ['last_searched_at' => now()]
    );

    DB::table('search_analytics')->where('query', mb_strtolower($query))->increment('search_count');
  }

  private function expandTerms(string $query): array
  {
    $terms = collect(preg_split('/\s+/', mb_strtolower($query)) ?: [])->filter()->values();
    $synonyms = DB::table('search_synonyms')->whereIn('term', $terms)->pluck('synonym');

    return $terms->merge($synonyms)->unique()->all();
  }
}
