<?php

namespace ReadWorth\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookPurchaseApplyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category' => 'required|string',
            'title' => 'required|string',
            'reason' => 'required|string',
            'price' => 'required',
            'description' => '',
            'image' => '',
            'url' => '',
        ];
    }

    public function attributes(): array
    {
        return [
            'category' => 'カテゴリ名',
            'title' => 'タイトル',
            'reason' => '申請理由',
            'price' => '価格',
            'description' => '本の説明',
            'image' => '画像',
            'url' => 'URL',
        ];
    }
}
