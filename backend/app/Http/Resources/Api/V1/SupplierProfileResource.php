<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'company_name' => $this->company_name, 'slug' => $this->slug, 'status' => $this->status, 'tax_id' => $this->when($request->user()?->hasRole('admin', 'super-admin'), $this->tax_id), 'contact_email' => $this->contact_email, 'contact_phone' => $this->contact_phone, 'metadata' => $this->metadata, 'addresses' => $this->whenLoaded('addresses'), 'documents' => $this->whenLoaded('documents'), 'certifications' => $this->whenLoaded('certifications')];
    }
}
