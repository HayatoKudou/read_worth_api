<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '名前',
            'email' => 'メールアドレス',
        ];
    }
}
