<?php

namespace App\Http\Requests\Shopping;

use Illuminate\Foundation\Http\FormRequest;

class InternationalEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['country' => ['required', 'string', 'size:2'], 'currency' => ['nullable', 'string', 'size:3'], 'locale' => ['nullable', 'string', 'max:10']];
    }
}
