<?php

namespace App\Http\Requests\Client;

use App\Models\Plan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        $workspaceId = $this->route()->parameter('clientId');
        return [
            'name' => [
                Rule::unique('clients')->ignore($workspaceId),
                'string',
                'max:255',
            ],
            'plan' => [
                'string',
                Rule::in(Plan::PLANS),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '組織名',
            'plan' => 'プラン',
        ];
    }
}
