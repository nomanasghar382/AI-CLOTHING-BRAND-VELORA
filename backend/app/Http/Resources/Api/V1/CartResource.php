<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->items->map(fn ($item) => ['id' => $item->id, 'product_id' => $item->product_id, 'product_variant_id' => $item->product_variant_id, 'name' => $item->product->name, 'sku' => $item->variant?->sku ?? $item->product->sku, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price, 'line_total' => number_format((float) $item->unit_price * $item->quantity, 2, '.', '')]);

        return ['id' => $this->id, 'currency' => $this->currency, 'items' => $items, 'subtotal' => number_format($items->sum(fn ($item) => (float) $item['line_total']), 2, '.', '')];
    }
}
