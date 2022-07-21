<?php

namespace App\Http\Requests\BookPurchaseApply;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'message' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'タイトル',
            'message' => 'メッセージ',
        ];
    }
}
