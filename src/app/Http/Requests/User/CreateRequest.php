<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'roles' => 'array',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '名前',
            'email' => 'メールアドレス',
            'roles' => 'ロール',
        ];
    }
}
