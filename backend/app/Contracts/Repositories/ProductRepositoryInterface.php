<?php

namespace App\Contracts\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function create(array $attributes): Product;

    public function update(Product $product, array $attributes): Product;

    public function delete(Product $product): void;
}
