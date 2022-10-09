<?php

namespace App\Http\Requests\BookReview;

use Illuminate\Foundation\Http\FormRequest;
use ReadWorth\Infrastructure\EloquentModel\BookReview;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'rate' => 'required|int|min:1',
            'review' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'rate' => 'レート',
            'review' => 'レビュー',
        ];
    }

    public function createBookReview(): BookReview
    {
        return new BookReview($this->validated());
    }
}
