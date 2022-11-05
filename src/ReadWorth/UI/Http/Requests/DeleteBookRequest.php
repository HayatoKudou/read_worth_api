<?php

namespace ReadWorth\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBookRequest extends FormRequest
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
