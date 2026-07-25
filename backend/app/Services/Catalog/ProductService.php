<?php

namespace App\Services\Catalog;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\User;

final class ProductService
{
    public function __construct(private readonly ProductRepositoryInterface $products) {}

    public function create(User $user, array $attributes): Product
    {
        $attributes['supplier_id'] = $user->hasRole('super-admin', 'admin')
            ? $attributes['supplier_id'] ?? null
            : $user->id;

        return $this->products->create($this->prepareAttributes($attributes));
    }

    public function update(Product $product, array $attributes): Product
    {
        unset($attributes['supplier_id']);

        return $this->products->update($product, $this->prepareAttributes($attributes));
    }

    public function delete(Product $product): void
    {
        $this->products->delete($product);
    }

    private function prepareAttributes(array $attributes): array
    {
        if (($attributes['status'] ?? null) === 'published' && empty($attributes['published_at'])) {
            $attributes['published_at'] = now();
        }

        return $attributes;
    }
}
