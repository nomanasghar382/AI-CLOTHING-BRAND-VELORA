<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'order_id' => $this->order_id, 'order_number' => $this->whenLoaded('order', fn () => $this->order->number),
            'customer' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name, 'email' => $this->user->email]),
            'type' => $this->type, 'status' => $this->status, 'reason' => $this->reason, 'admin_note' => $this->admin_note,
            'requested_amount' => $this->requested_amount, 'refund_amount' => $this->refund_amount,
            'resolved_at' => $this->resolved_at?->toIso8601String(), 'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
