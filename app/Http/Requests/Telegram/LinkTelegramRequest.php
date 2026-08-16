<?php

namespace App\Http\Requests\Telegram;

use Illuminate\Foundation\Http\FormRequest;

class LinkTelegramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
       public function rules(): array
    {
        return [
            'telegram_chat_id' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'telegram_chat_id.numeric' => 'معرّف تليجرام يجب أن يكون رقماً صحيحاً',
        ];
    }
}
