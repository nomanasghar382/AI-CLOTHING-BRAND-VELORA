<?php

namespace App\Services\Loyalty;

use App\Models\Product;
use App\Models\ProductAlert;
use App\Notifications\ProductAlertNotification;
use Illuminate\Support\Facades\DB;

final class ProductAlertService
{
    public function dispatchEligible(Product $product): int
    {
        $price = (float) ($product->sale_price ?? $product->price);
        $inStock = $product->stock_quantity > 0;

        return DB::transaction(function () use ($product, $price, $inStock): int {
            $alerts = ProductAlert::query()->with(['user', 'product'])
                ->where('product_id', $product->id)->where('is_active', true)
                ->where(function ($query) use ($price, $inStock) {
                    $query->where(fn ($q) => $q->where('type', 'price')->where('target_price', '>=', $price))
                        ->orWhere(fn ($q) => $q->where('type', 'restock')->whereRaw($inStock ? '1 = 1' : '1 = 0'));
                })->lockForUpdate()->get();
            foreach ($alerts as $alert) {
                $allowed = $alert->type === 'price' ? $alert->user->notificationPreference?->price_alerts !== false : $alert->user->notificationPreference?->restock_alerts !== false;
                if ($allowed) {
                    DB::afterCommit(fn () => $alert->user->notify(new ProductAlertNotification($alert)));
                }
                $alert->update(['notified_at' => now(), 'is_active' => false]);
            }

            return $alerts->count();
        });
    }
}
