<?php

namespace App\Http\Requests\Book;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bookCategoryName' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => '',
            'image' => '',
        ];
    }

    public function store()
    {
        return $this->validated();
    }
}
