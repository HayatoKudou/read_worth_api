<?php

namespace App\Http\Requests\BookCategory;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
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
