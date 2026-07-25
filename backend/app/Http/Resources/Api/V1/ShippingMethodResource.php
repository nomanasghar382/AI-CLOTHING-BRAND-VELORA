<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name, 'code' => $this->code, 'description' => $this->description,
            'is_active' => $this->is_active, 'rates' => $this->whenLoaded('rates', fn () => $this->rates->map(fn ($rate) => [
                'id' => $rate->id, 'country' => $rate->country, 'amount' => $rate->amount,
                'free_shipping_threshold' => $rate->free_shipping_threshold, 'is_active' => $rate->is_active,
            ])),
        ];
    }
}
