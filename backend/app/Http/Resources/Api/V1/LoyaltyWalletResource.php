<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyWalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'points_balance' => $this->points_balance,
            'store_credit_balance' => $this->store_credit_balance,
            'vip_tier' => $this->vip_tier,
            'transactions' => $this->whenLoaded('transactions', fn () => $this->transactions->map(fn ($transaction) => ['id' => $transaction->id, 'type' => $transaction->type, 'points_delta' => $transaction->points_delta, 'credit_delta' => $transaction->credit_delta, 'created_at' => $transaction->created_at])),
        ];
    }
}
