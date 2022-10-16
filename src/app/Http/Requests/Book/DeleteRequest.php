<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'book_ids' => 'required|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'book_ids' => '書籍ID',
        ];
    }
}
