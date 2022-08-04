<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignInGoogleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'accessToken' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'メールアドレス',
            'accessToken' => 'アクセストークン',
        ];
    }
}
