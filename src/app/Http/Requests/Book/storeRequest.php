<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bookCategoryName' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => '',
            'image' => '',
        ];
    }
}
