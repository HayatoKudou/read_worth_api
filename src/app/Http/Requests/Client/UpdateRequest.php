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
            'plan' => [
                'string',
                Rule::in(Plan::PLANS),
            ],
        ];
    }
}
