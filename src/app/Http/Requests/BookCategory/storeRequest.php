<?php

namespace App\Http\Requests\BookCategory;

use App\Models\BookCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function makePost(): BookCategory
    {
        return new BookCategory($this->validated());
    }
}
