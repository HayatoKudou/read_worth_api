<?php

namespace ReadWorth\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRentalApplyRequest extends FormRequest
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
}
