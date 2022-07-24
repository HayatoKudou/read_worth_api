<?php

namespace App\Http\Requests\Book;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'タイトル',
        ];
    }
}
