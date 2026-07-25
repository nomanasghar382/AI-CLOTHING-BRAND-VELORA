<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'items' => $this->items->map(fn ($item) => ['id' => $item->id, 'product_id' => $item->product_id, 'product_variant_id' => $item->product_variant_id, 'name' => $item->product->name, 'slug' => $item->product->slug, 'added_at' => $item->created_at?->toIso8601String()])];
    }
}
