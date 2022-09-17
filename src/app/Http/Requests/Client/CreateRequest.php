<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:clients|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '組織名',
        ];
    }
}
