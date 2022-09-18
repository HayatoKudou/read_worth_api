<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:workspaces|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'ワークスペース名',
        ];
    }
}
