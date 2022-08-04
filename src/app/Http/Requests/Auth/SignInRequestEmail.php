<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignInRequestEmail extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'メールアドレス',
            'password' => 'パスワード',
        ];
    }
}
