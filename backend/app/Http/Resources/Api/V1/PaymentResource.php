<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'order_id' => $this->order_id, 'order_number' => $this->whenLoaded('order', fn () => $this->order->number),
            'provider' => $this->provider, 'status' => $this->status, 'amount' => $this->amount, 'currency' => $this->currency,
            'provider_reference' => $this->provider_reference, 'created_at' => $this->created_at?->toIso8601String(),
            'transactions' => $this->whenLoaded('transactions', fn () => $this->transactions->map(fn ($transaction) => [
                'id' => $transaction->id, 'type' => $transaction->type, 'status' => $transaction->status,
                'amount' => $transaction->amount, 'provider_reference' => $transaction->provider_reference,
                'created_at' => $transaction->created_at?->toIso8601String(),
            ])),
        ];
    }
}
