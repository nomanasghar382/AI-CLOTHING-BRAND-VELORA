<?php

namespace App\Http\Requests\Style;

use Illuminate\Foundation\Http\FormRequest;

final class SavedOutfitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:150'], 'occasion' => ['nullable', 'string', 'max:100'], 'notes' => ['nullable', 'string', 'max:1000'],
            'recommendation_id' => ['nullable', 'integer', 'exists:style_recommendations,id'], 'product_ids' => ['required', 'array', 'min:1', 'max:12'], 'product_ids.*' => ['integer', 'exists:products,id']];
    }
}
