<?php

namespace App\Http\Requests\Creator;

use Illuminate\Foundation\Http\FormRequest;

final class WardrobeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['product_id' => ['nullable', 'integer', 'exists:products,id'], 'name' => ['required', 'string', 'max:200'], 'image_url' => ['nullable', 'url', 'max:2048'], 'category' => ['nullable', 'string', 'max:100'], 'color' => ['nullable', 'string', 'max:50'], 'metadata' => ['nullable', 'array']];
    }
}
