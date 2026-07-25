<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EthicalPassportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'verification_reference' => $this->verification_reference,
            'verified_by' => $this->verified_by,
            'verified_at' => $this->verified_at,
            'expires_at' => $this->expires_at,
            'source_url' => $this->source_url,
            'claims' => $this->claims,
            'evidence' => $this->whenLoaded('evidence', fn () => $this->evidence->map(fn ($item) => ['category' => $item->category, 'title' => $item->title, 'issuer' => $item->issuer, 'reference' => $item->reference, 'document_url' => $item->document_url, 'verified_at' => $item->verified_at, 'expires_at' => $item->expires_at])),
        ];
    }
}
