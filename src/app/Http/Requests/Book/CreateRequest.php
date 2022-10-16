<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category' => 'required|string',
            'title' => 'required|string|max:255',
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
