<?php

namespace App\Http\Requests\FeedBack;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'message' => 'メッセージ',
        ];
    }
}
