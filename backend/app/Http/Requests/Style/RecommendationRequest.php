<?php

namespace App\Http\Requests\Style;

use Illuminate\Foundation\Http\FormRequest;

final class RecommendationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['occasion' => ['nullable', 'string', 'max:100'], 'budget' => ['nullable', 'numeric', 'min:0'],
            'city' => ['nullable', 'string', 'max:100'], 'notes' => ['nullable', 'string', 'max:1000'],
            'conversation_id' => ['nullable', 'integer', 'exists:style_conversations,id']];
    }
}
