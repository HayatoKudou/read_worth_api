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
            'enable_purchase_limit' => 'boolean',
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
            'enable_purchase_limit' => '購入補助金上限有効化',
            'purchase_limit' => '購入補助金上限',
            'purchase_limit_unit' => '購入補助金上限単位',
            'private_ownership_allow' => '個人所有の許可',
            'plan' => 'プラン',
        ];
    }
}
