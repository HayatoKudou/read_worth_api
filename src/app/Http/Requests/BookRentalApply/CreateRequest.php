<?php

namespace App\Http\Requests\BookRentalApply;

use Illuminate\Foundation\Http\FormRequest;
use ReadWorth\Infrastructure\EloquentModel\BookRentalApply;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reason' => 'required|string',
            'expected_return_date' => 'required|date',
        ];
    }

    public function attributes(): array
    {
        return [
            'reason' => '申請理由',
            'expected_return_date' => '返却予定日',
        ];
    }

    public function createBookRentalApply(): BookRentalApply
    {
        return new BookRentalApply($this->validated());
    }
}
