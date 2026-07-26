<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CatalogController extends Controller
{
    use RespondsWithApi;

    public function products(Request $request): JsonResponse
    {
        $query = Product::query()->with([
            'brand',
            'category',
            'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')->limit(1),
            'colors',
            'sizes',
        ])->where('status', 'published');
        if ($request->filled('q')) { $term = $request->string('q')->toString(); $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('fabric', 'like', "%{$term}%")->orWhere('material', 'like', "%{$term}%")); }
        if ($request->filled('category')) $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        if ($request->filled('brand')) $query->whereHas('brand', fn ($q) => $q->where('slug', $request->string('brand')));
        if ($request->filled('brands')) {
            $brands = explode(',', (string) $request->string('brands'));
            $query->whereHas('brand', fn ($q) => $q->whereIn('slug', $brands));
        }
        if ($request->filled('color')) $query->whereHas('colors', fn ($q) => $q->where('slug', $request->string('color')));
        if ($request->filled('colors')) {
            $colors = explode(',', (string) $request->string('colors'));
            $query->whereHas('colors', fn ($q) => $q->whereIn('slug', $colors));
        }
        if ($request->filled('size')) $query->whereHas('sizes', fn ($q) => $q->where('slug', $request->string('size')));
        if ($request->filled('material')) $query->where('material', $request->string('material'));
        if ($request->filled('fabric')) $query->where('fabric', $request->string('fabric'));
        if ($request->filled('coverage_level')) $query->where('coverage_level', $request->string('coverage_level'));
        if ($request->filled('gender')) $query->where('gender', $request->string('gender'));
        if ($request->filled('line')) $query->where('catalog_line', $request->string('line'));
        if ($request->filled('season')) $query->where('season', $request->string('season'));
        if ($request->filled('min_price')) $query->where('price', '>=', $request->float('min_price'));
        if ($request->filled('max_price')) $query->where('price', '<=', $request->float('max_price'));
        if ($request->boolean('in_stock')) $query->where('stock_quantity', '>', 0);
        foreach (['featured' => 'is_featured', 'trending' => 'is_trending', 'new' => 'is_new_arrival'] as $input => $column) if ($request->boolean($input)) $query->where($column, true);
        if ($request->boolean('sale')) $query->whereNotNull('sale_price');
        match ($request->string('sort', 'newest')->toString()) { 'price_asc' => $query->orderByRaw('COALESCE(sale_price, price)'), 'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'), 'oldest' => $query->oldest('published_at'), 'popular' => $query->orderByDesc('views_count'), 'best_selling' => $query->orderByDesc('sales_count'), 'alphabetical' => $query->orderBy('name'), default => $query->latest('published_at') };
        $page = $query->paginate(min(max($request->integer('per_page', 12), 1), 48))->withQueryString();
        return $this->success(['items' => ProductResource::collection($page->items()), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()]]);
    }

    public function show(Product $product): JsonResponse { abort_unless($product->status === 'published', 404); $product->increment('views_count'); return $this->success(new ProductResource($product->load(['brand', 'category', 'images', 'colors', 'sizes', 'variants.color', 'variants.size', 'matchedProduct.brand', 'matchedProduct.images']))); }
    public function categories(): JsonResponse { return $this->success(CategoryResource::collection(Category::query()->whereNull('parent_id')->where('status', 'active')->with('children')->get())); }
    public function filters(): JsonResponse
    {
        $published = Product::query()->where('status', 'published')->where('gender', 'men');
        $sportParentId = Category::query()->where('slug', 'mens-sport')->value('id');

        return $this->success([
            'brands' => Brand::query()->where('status', 'active')->orderBy('name')->get(['name', 'slug']),
            'colors' => Color::query()->where('status', 'active')->get(['name', 'slug', 'hex_code']),
            'sizes' => Size::query()->where('status', 'active')->orderBy('sort_order')->get(['name', 'slug', 'international_size']),
            'men_categories' => Category::query()->where('parent_id', $sportParentId)->where('status', 'active')->orderBy('name')->get(['name', 'slug']),
            'catalog_lines' => (clone $published)->whereNotNull('catalog_line')->distinct()->orderBy('catalog_line')->pluck('catalog_line'),
            'materials' => (clone $published)->whereNotNull('material')->distinct()->orderBy('material')->pluck('material'),
            'fabrics' => (clone $published)->whereNotNull('fabric')->distinct()->orderBy('fabric')->pluck('fabric'),
            'occasions' => (clone $published)->whereNotNull('season')->distinct()->orderBy('season')->pluck('season'),
            'price_range' => [
                'min' => (float) ((clone $published)->min('price') ?? 0),
                'max' => (float) ((clone $published)->max('price') ?? 0),
            ],
            'in_stock_count' => (clone $published)->where('stock_quantity', '>', 0)->count(),
        ]);
    }
}
