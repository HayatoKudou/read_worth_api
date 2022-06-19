<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => '',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email',
            'password' => 'required|string|max:255|confirmed',
            'roles' => '',
        ];
    }

    public function createUser(): User
    {
        return new User($this->validated());
    }
}
