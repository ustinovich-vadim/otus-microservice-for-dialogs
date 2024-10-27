<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class MarkAsReadMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message_id' => 'required|exists:messages,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'message_id' => $this->route('message_id'),
        ]);
    }
}
