<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bookIds' => 'required|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'bookIds' => '書籍ID',
        ];
    }
}
