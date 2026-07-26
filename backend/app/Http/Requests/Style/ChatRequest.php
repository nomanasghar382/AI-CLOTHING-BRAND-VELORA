<?php

namespace App\Http\Requests\Style;

use Illuminate\Foundation\Http\FormRequest;

final class ChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['conversation_id' => ['nullable', 'integer', 'exists:style_conversations,id'], 'message' => ['required', 'string', 'max:2000'],
            'occasion' => ['nullable', 'string', 'max:100'], 'budget' => ['nullable', 'numeric', 'min:0'], 'city' => ['nullable', 'string', 'max:100']];
    }
}
