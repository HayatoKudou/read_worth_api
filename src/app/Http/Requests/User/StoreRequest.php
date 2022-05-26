<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => '',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email',
            'password' => '',
            'roles' => '',
        ];
    }

    public function makePost(): User
    {
        return new User($this->validated());
    }
}
