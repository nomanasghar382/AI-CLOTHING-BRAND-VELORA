<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class SupplierProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('supplier', 'admin', 'super-admin') ?? false;
    }

    public function rules(): array
    {
        return ['company_name' => ['required', 'string', 'max:255'], 'tax_id' => ['nullable', 'string', 'max:100'], 'contact_email' => ['nullable', 'email'], 'contact_phone' => ['nullable', 'string', 'max:50'], 'metadata' => ['nullable', 'array']];
    }
}
