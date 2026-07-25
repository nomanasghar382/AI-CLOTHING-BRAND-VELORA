<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name, 'slug' => $this->slug, 'short_description' => $this->short_description,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price, 'sale_price' => $this->sale_price,
            'discount_percent' => $this->sale_price ? (int) round((1 - $this->sale_price / $this->price) * 100) : 0,
            'fabric' => $this->fabric, 'material' => $this->material, 'fit_type' => $this->fit_type, 'coverage_level' => $this->coverage_level,
            'gender' => $this->gender, 'season' => $this->season, 'stock_quantity' => $this->stock_quantity,
            'is_featured' => $this->is_featured, 'is_trending' => $this->is_trending, 'is_new_arrival' => $this->is_new_arrival,
            'brand' => $this->whenLoaded('brand', fn () => ['name' => $this->brand?->name, 'slug' => $this->brand?->slug]),
            'category' => $this->whenLoaded('category', fn () => ['name' => $this->category?->name, 'slug' => $this->category?->slug]),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($image) => ['url' => $image->url, 'thumbnail_url' => $image->thumbnail_url, 'alt_text' => $image->alt_text, 'is_primary' => $image->is_primary])),
            'colors' => $this->whenLoaded('colors', fn () => $this->colors->map(fn ($color) => ['name' => $color->name, 'hex_code' => $color->hex_code])),
            'sizes' => $this->whenLoaded('sizes', fn () => $this->sizes->map(fn ($size) => ['name' => $size->name, 'international_size' => $size->international_size])),
        ];
    }
}
