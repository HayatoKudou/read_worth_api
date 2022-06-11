<?php

namespace App\Http\Requests\BookRentalApply;

use App\Models\BookRentalApply;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reason' => 'required|string',
            'expected_return_date' => 'required|date',
        ];
    }

    public function createBookRentalApply(): BookRentalApply
    {
        return new BookRentalApply($this->validated());
    }
}
