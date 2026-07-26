<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name, 'country' => $this->country, 'state' => $this->state,
            'postal_code' => $this->postal_code, 'rate' => $this->rate, 'is_active' => $this->is_active,
        ];
    }
}
