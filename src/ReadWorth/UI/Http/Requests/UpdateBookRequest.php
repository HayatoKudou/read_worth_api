<?php

namespace ReadWorth\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'max:255',
            'category' => 'required|string',
            'status' => 'required|int',
            'image' => '',
            'url' => '',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'タイトル',
            'description' => '本の説明',
            'category' => 'カテゴリ',
            'status' => 'ステータス',
        ];
    }
}
