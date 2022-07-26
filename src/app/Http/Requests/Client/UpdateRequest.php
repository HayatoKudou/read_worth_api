<?php

namespace App\Http\Requests\Client;

use App\Models\Plan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        $clientId = $this->route()->parameter('clientId');
        return [
            'name' => [
                Rule::unique('clients')->ignore($clientId),
                'string',
                'max:255',
            ],
            'purchase_limit' => 'int',
            'purchase_limit_unit' => 'string',
            'private_ownership_allow' => 'boolean',
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
            'purchase_limit' => '名前',
            'purchase_limit_unit' => '名前',
            'private_ownership_allow' => '名前',
            'plan' => '名前',
        ];
    }
}
