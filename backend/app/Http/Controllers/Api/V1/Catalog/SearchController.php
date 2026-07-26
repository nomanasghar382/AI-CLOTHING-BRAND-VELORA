<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use App\Services\Catalog\SearchService;
use App\Services\Search\VisualSearchService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SearchController extends Controller
{
  use RespondsWithApi;

  public function __construct(private readonly SearchService $search)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $page = $this->search->search(
      $request->all(),
      $request->user()?->id,
      $request->header('X-Session-Id')
    );

    return $this->success([
      'items' => ProductResource::collection($page->items()),
      'meta' => [
        'current_page' => $page->currentPage(),
        'last_page' => $page->lastPage(),
        'per_page' => $page->perPage(),
        'total' => $page->total(),
      ],
    ]);
  }

  public function suggestions(Request $request): JsonResponse
  {
    return $this->success([
      'suggestions' => $this->search->suggestions((string) $request->string('q')),
      'popular' => $this->search->popular(),
      'recent' => $this->search->recent($request->user()?->id, $request->header('X-Session-Id')),
    ]);
  }

  public function visual(Request $request, VisualSearchService $searches): JsonResponse
  {
    $request->validate(['image' => 'required|image|max:10240']);
    $matches = $searches->searchCatalog($request->file('image'));
    $productIds = collect($matches)->pluck('product_id')->filter()->all();
    $products = Product::query()->with(['brand', 'images'])->whereIn('id', $productIds)->get()->keyBy('id');
    $items = collect($matches)->map(function (array $match) use ($products) {
      $product = $products->get($match['product_id']);
      if (! $product) {
        return null;
      }

      return [
        'product_id' => $product->id,
        'slug' => $product->slug,
        'name' => $product->name,
        'brand' => $product->brand?->name,
        'price' => (float) ($product->sale_price ?? $product->price),
        'score' => $match['score'] ?? 0,
        'image_url' => $match['image_url'] ?? $product->images->first()?->url,
      ];
    })->filter()->values();

    return $this->success(['items' => $items, 'provider' => config('services.visual_search.key') ? config('services.visual_search.provider') : 'catalog_fallback'], 'Visual search complete.');
  }
}
