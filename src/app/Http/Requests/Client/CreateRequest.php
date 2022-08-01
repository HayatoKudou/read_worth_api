<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'plan' => 'required|string',
            'name' => 'required|unique:clients|string|max:255',
            'userId' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'plan' => 'プラン',
            'name' => '組織名',
        ];
    }
}
