<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'max:255',
            'image' => '',
            'category' => 'required|string',
            'status' => 'required',
        ];
    }
}
