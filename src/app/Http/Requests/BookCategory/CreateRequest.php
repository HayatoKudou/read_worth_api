<?php

namespace App\Http\Requests\BookCategory;

use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(Request $request): array
    {
        $clientId = $request->route()->parameter('clientId');
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('book_category', 'name')->where(function ($query) use ($clientId): void {
                    $query->where('client_id', $clientId);
                }),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'カテゴリ名',
        ];
    }

    public function createBookCategory(): BookCategory
    {
        return new BookCategory($this->validated());
    }
}
