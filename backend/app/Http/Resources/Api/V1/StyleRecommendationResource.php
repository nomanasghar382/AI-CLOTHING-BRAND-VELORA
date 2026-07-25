<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StyleRecommendationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'], 'kind' => $this['kind'], 'provider' => $this['provider'],
            'reply' => json_decode($this['response'] ?? '{}', true)['reply'] ?? '',
            'weather' => json_decode($this['weather'] ?? 'null', true), 'generated_at' => $this['generated_at'],
            'items' => collect($this['items'] ?? [])->map(fn ($item) => [
                'product_id' => $item->product_id, 'name' => $item->product_name, 'price' => $item->sale_price ?? $item->product_price,
                'rank' => $item->rank, 'reason' => $item->reason, 'score' => $item->score,
            ])->values(),
        ];
    }
}
