<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;

class ExcludeUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'userIds' => 'required',
        ];
    }
}
