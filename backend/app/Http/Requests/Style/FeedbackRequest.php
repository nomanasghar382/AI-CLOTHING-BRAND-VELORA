<?php

namespace App\Http\Requests\Style;

use Illuminate\Foundation\Http\FormRequest;

final class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['recommendation_id' => ['nullable', 'integer', 'exists:style_recommendations,id'], 'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'type' => ['required', 'string', 'max:32'], 'rating' => ['nullable', 'integer', 'between:1,5'], 'comment' => ['nullable', 'string', 'max:1000'], 'metadata' => ['nullable', 'array']];
    }
}
