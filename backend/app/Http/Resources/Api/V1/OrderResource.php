<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'number' => $this->number, 'status' => $this->status, 'payment_status' => $this->payment_status, 'currency' => $this->currency, 'locale' => $this->locale, 'exchange_rate' => $this->exchange_rate, 'subtotal' => $this->subtotal, 'discount_total' => $this->discount_total, 'shipping_total' => $this->shipping_total, 'tax_total' => $this->tax_total, 'duty_total' => $this->duty_total, 'grand_total' => $this->grand_total, 'created_at' => $this->created_at?->toIso8601String(), 'customer' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name, 'email' => $this->user->email]), 'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => ['id' => $item->id, 'product_id' => $item->product_id, 'product_variant_id' => $item->product_variant_id, 'product_name' => $item->product_name, 'sku' => $item->sku, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price, 'line_total' => $item->line_total])), 'shipping_address' => $this->whenLoaded('shippingAddress', fn () => $this->shippingAddress ? $this->address($this->shippingAddress) : null), 'billing_address' => $this->whenLoaded('billingAddress', fn () => $this->billingAddress ? $this->address($this->billingAddress) : null), 'shipping_method' => $this->whenLoaded('shippingMethod', fn () => $this->shippingMethod ? ['id' => $this->shippingMethod->id, 'name' => $this->shippingMethod->name, 'code' => $this->shippingMethod->code] : null), 'status_history' => $this->whenLoaded('history', fn () => $this->history->map(fn ($history) => ['from_status' => $history->from_status, 'to_status' => $history->to_status, 'note' => $history->note, 'created_at' => $history->created_at?->toIso8601String()])), 'payments' => PaymentResource::collection($this->whenLoaded('payments')), 'return_requests' => ReturnRequestResource::collection($this->whenLoaded('returnRequests'))];
    }

    private function address(object $address): array
    {
        return ['first_name' => $address->first_name, 'last_name' => $address->last_name, 'phone' => $address->phone, 'line1' => $address->line1, 'line2' => $address->line2, 'city' => $address->city, 'state' => $address->state, 'postal_code' => $address->postal_code, 'country' => $address->country];
    }
}
