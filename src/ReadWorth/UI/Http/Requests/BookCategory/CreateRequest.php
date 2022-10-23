<?php

namespace ReadWorth\UI\Http\Requests\BookCategory;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(Request $request): array
    {
        $workspaceId = $request->route()->parameter('workspaceId');
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('book_category')->where('workspace_id', $workspaceId),
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
