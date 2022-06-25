<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
        ];
    }
}
