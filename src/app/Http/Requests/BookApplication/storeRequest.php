<?php

namespace App\Http\Requests\BookApplication;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bookCategoryName' => 'required|string',
            'title' => 'required|string',
            'reason' => 'required|string',
            'description' => '',
            'image' => '',
        ];
    }
}
