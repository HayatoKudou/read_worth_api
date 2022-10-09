<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use ReadWorth\Infrastructure\EloquentModel\Plan;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        $workspaceId = $this->route()->parameter('workspaceId');
        return [
            'name' => [
                Rule::unique('workspaces')->ignore($workspaceId),
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
            'name' => 'ワークスペース名',
            'plan' => 'プラン',
        ];
    }
}
