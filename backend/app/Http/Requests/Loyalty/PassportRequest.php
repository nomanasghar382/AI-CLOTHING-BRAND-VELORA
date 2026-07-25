<?php

namespace App\Http\Requests\Loyalty;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PassportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super-admin', 'admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'status' => ['required', Rule::in(['draft', 'verified', 'suspended'])],
            'verification_reference' => ['required', 'string', 'max:128'],
            'verified_by' => ['required', 'string', 'max:255'],
            'verified_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date', 'after:verified_at'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'claims' => ['required', 'array'],
            'evidence' => ['required_if:status,verified', 'array', 'min:1'],
            'evidence.*.category' => ['required_with:evidence', 'string', 'max:64'],
            'evidence.*.title' => ['required_with:evidence', 'string', 'max:255'],
            'evidence.*.issuer' => ['required_with:evidence', 'string', 'max:255'],
            'evidence.*.reference' => ['required_with:evidence', 'string', 'max:255'],
            'evidence.*.document_url' => ['nullable', 'url', 'max:2048'],
            'evidence.*.verified_at' => ['required_with:evidence', 'date'],
            'evidence.*.expires_at' => ['nullable', 'date'],
        ];
    }
}
