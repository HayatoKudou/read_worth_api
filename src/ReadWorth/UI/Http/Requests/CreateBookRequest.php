<?php

namespace ReadWorth\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => '',
            'image' => '',
            'url' => '',
        ];
    }

    public function attributes(): array
    {
        return [
            'category' => 'カテゴリ',
            'title' => 'タイトル',
        ];
    }
}
