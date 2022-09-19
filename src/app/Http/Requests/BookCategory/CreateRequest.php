<?php

namespace App\Http\Requests\BookCategory;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(Request $request): array
    {
        $workspaceId = $request->route()->parameter('clientId');
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('book_category')->ignore($workspaceId),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'カテゴリ名',
        ];
    }
}
