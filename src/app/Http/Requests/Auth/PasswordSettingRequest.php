<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class PasswordSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|string|max:255|confirmed'
        ];
    }
}