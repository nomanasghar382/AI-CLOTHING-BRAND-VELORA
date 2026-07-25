<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\Loyalty\ProductAlertService;

final class ProductObserver
{
    public function updated(Product $product): void
    {
        if ($product->wasChanged(['price', 'sale_price', 'stock_quantity'])) {
            app(ProductAlertService::class)->dispatchEligible($product);
        }
    }
}
