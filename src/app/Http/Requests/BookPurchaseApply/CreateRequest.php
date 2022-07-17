<?php

namespace App\Http\Requests\BookPurchaseApply;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bookCategoryName' => 'required|string',
            'title' => 'required|string',
            'reason' => 'required|string',
            'price' => 'required',
            'description' => '',
            'image' => '',
        ];
    }

    public function attributes(): array
    {
        return [
            'bookCategoryName' => 'カテゴリ名',
            'title' => 'タイトル',
            'reason' => '申請理由',
            'price' => '価格',
            'description' => '本の説明',
            'image' => '画像',
        ];
    }
}
