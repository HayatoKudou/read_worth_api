<?php

namespace App\Http\Requests\Book;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'categoryId' => '',
            'title' => 'required|string|max:255',
            'description' => '',
            'image' => '',
        ];
    }

    public function makePost(): Book
    {
        return new Book($this->validated());
    }
}
