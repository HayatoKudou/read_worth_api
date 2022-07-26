<?php

namespace App\Http\Requests\Client;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:clients|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '組織名',
        ];
    }

    public function createClient(): Client
    {
        return new Client($this->validated());
    }
}
