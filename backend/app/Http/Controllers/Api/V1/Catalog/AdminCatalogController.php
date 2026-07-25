<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreProductRequest;
use App\Http\Requests\Catalog\UpdateProductRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use App\Services\Catalog\ProductService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminCatalogController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly ProductService $products) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->with(['brand', 'category', 'images', 'colors', 'sizes'])->latest();

        if (! $request->user()->hasRole('super-admin', 'admin')) {
            $query->where('supplier_id', $request->user()->id);
        }

        return $this->success(ProductResource::collection($query->paginate(20)));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->products->create($request->user(), $request->validated());

        return $this->success(new ProductResource($product->load(['brand', 'category', 'images', 'colors', 'sizes'])), 'Product created successfully.', 201);
    }

    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return $this->success(new ProductResource($product->load(['brand', 'category', 'images', 'colors', 'sizes'])));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->products->update($product, $request->validated());

        return $this->success(new ProductResource($product->load(['brand', 'category', 'images', 'colors', 'sizes'])), 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->authorize('delete', $product);
        $this->products->delete($product);

        return $this->success(null, 'Product deleted successfully.');
    }
}
