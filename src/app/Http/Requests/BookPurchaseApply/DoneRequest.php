<?php

namespace App\Http\Requests\BookPurchaseApply;

use Illuminate\Foundation\Http\FormRequest;

class DoneRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'location' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'location' => '置き場所',
        ];
    }
}
