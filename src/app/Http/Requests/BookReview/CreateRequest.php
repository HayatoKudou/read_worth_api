<?php

namespace App\Http\Requests\BookReview;

use App\Models\BookReview;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'rate' => 'required|int|min:1',
            'review' => 'required|string',
        ];
    }

    public function createBookReview(): BookReview
    {
        return new BookReview($this->validated());
    }
}
