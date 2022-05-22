<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

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
