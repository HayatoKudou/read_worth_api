<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
            'client_id' => '',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email',
            'password' => 'required|confirmed',
            'roles' => '',
        ];
    }
}
