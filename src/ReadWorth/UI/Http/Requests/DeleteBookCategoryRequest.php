<?php

namespace ReadWorth\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBookCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'カテゴリ名',
        ];
    }
}
