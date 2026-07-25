<?php

namespace App\Http\Requests\Shopping;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $address = ['first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'], 'phone' => ['required', 'string', 'max:40'], 'line1' => ['required', 'string', 'max:255'], 'line2' => ['nullable', 'string', 'max:255'], 'city' => ['required', 'string', 'max:100'], 'state' => ['nullable', 'string', 'max:100'], 'postal_code' => ['required', 'string', 'max:32'], 'country' => ['required', 'string', 'size:2']];
        $rules = ['shipping_rate_id' => ['nullable', 'integer', 'exists:shipping_rates,id'], 'coupon_code' => ['nullable', 'string', 'max:64'], 'payment_provider' => ['required', Rule::in(['cod', 'stripe', 'paypal'])], 'notes' => ['nullable', 'string', 'max:2000'], 'billing_address' => ['nullable', 'array']];
        foreach ($address as $field => $fieldRules) {
            $rules["shipping_address.{$field}"] = $fieldRules;
            $rules["billing_address.{$field}"] = in_array($field, ['first_name', 'last_name', 'phone', 'line1', 'city', 'postal_code', 'country'], true)
                ? array_merge(['required_with:billing_address'], array_values(array_filter($fieldRules, fn (string $rule) => $rule !== 'required')))
                : array_merge(['sometimes'], $fieldRules);
        }

        return $rules;
    }
}
