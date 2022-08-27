<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email',
            'password' => 'confirmed',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'ユーザーID',
            'name' => '名前',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
        ];
    }
}
