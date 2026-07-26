<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class InventoryAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('supplier', 'admin', 'super-admin') ?? false;
    }

    public function rules(): array
    {
        return ['quantity_delta' => ['required', 'integer', 'between:-1000000,1000000'], 'reason' => ['required', 'string', 'max:255'], 'idempotency_key' => ['nullable', 'string', 'max:100']];
    }
}
